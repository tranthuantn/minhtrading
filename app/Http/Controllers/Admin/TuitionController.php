<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\TuitionPayment;
use App\Models\User;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TuitionController extends Controller
{
    /**
     * Display a listing of students with tuition status, filters, and stats.
     */
    public function index(Request $request): View
    {
        $today = now()->startOfDay();

        $query = User::students()->with(['enrolledCourses', 'tuitionPayments' => function ($q) {
            $q->latest()->limit(1);
        }]);

        // Filter by course
        if ($request->filled('course_id')) {
            $courseId = (int) $request->input('course_id');
            $query->whereHas('courses', function ($q) use ($courseId) {
                $q->where('courses.id', $courseId);
            });
        }

        // Filter by search keyword (name, email, phone)
        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter by tuition status
        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'overdue') {
                // Quá hạn <= 30 ngày hoặc chưa có record
                $query->where(function ($q) use ($today) {
                    $q->whereNotIn('billing_type', ['full_course', 'free'])
                        ->where(function ($sq) use ($today) {
                            $sq->whereNull('paid_until')
                                ->orWhereBetween('paid_until', [$today->copy()->subDays(30), $today->copy()->subDay()]);
                        });
                });
            } elseif ($status === 'severely_overdue') {
                // Quá hạn > 30 ngày
                $query->whereNotIn('billing_type', ['full_course', 'free'])
                    ->whereNotNull('paid_until')
                    ->where('paid_until', '<', $today->copy()->subDays(30));
            } elseif ($status === 'due_soon') {
                // Sắp hết hạn trong 5 ngày tới
                $query->whereNotIn('billing_type', ['full_course', 'free'])
                    ->whereNotNull('paid_until')
                    ->whereBetween('paid_until', [$today, $today->copy()->addDays(5)]);
            } elseif ($status === 'active') {
                // Còn hạn > 5 ngày
                $query->whereNotIn('billing_type', ['full_course', 'free'])
                    ->whereNotNull('paid_until')
                    ->where('paid_until', '>', $today->copy()->addDays(5));
            } elseif ($status === 'full_course') {
                $query->where('billing_type', 'full_course');
            } elseif ($status === 'free') {
                $query->where('billing_type', 'free');
            }
        }

        $students = $query->orderBy('paid_until', 'asc')->paginate(15)->withQueryString();

        // Calculate comprehensive stats
        $allStudents = User::students()->get();
        $stats = [
            'total' => $allStudents->count(),
            'active' => 0,
            'due_soon' => 0,
            'overdue' => 0,
            'severely_overdue' => 0,
            'full_course' => 0,
            'free' => 0,
        ];

        foreach ($allStudents as $st) {
            $stCode = $st->tuition_status['code'] ?? 'no_record';
            if ($stCode === 'active') {
                $stats['active']++;
            } elseif ($stCode === 'due_soon') {
                $stats['due_soon']++;
            } elseif ($stCode === 'overdue' || $stCode === 'no_record') {
                $stats['overdue']++;
            } elseif ($stCode === 'severely_overdue') {
                $stats['severely_overdue']++;
            } elseif ($stCode === 'full_course') {
                $stats['full_course']++;
            } elseif ($stCode === 'free') {
                $stats['free']++;
            }
        }

        // Total revenue this month
        $stats['revenue_this_month'] = TuitionPayment::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount');

        $courses = Course::published()->orderBy('sort_order')->get();

        return view('admin.tuition.index', compact('students', 'stats', 'courses'));
    }

    /**
     * Show form to record a tuition payment.
     */
    public function create(Request $request): View
    {
        $students = User::students()->orderBy('name')->get();
        $courses = Course::published()->orderBy('sort_order')->get();
        $selectedStudentId = $request->input('student_id');
        $selectedStudent = $selectedStudentId ? User::with('tuitionPayments')->find($selectedStudentId) : null;

        return view('admin.tuition.create', compact('students', 'courses', 'selectedStudentId', 'selectedStudent'));
    }

    /**
     * Store a recorded tuition payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'course_id' => ['nullable', 'exists:courses,id'],
            'billing_type' => ['required', 'in:monthly,multi_month,full_course,custom'],
            'months_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'amount' => ['required', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date'],
            'payment_method' => ['required', 'in:bank_transfer,cash,crypto,other'],
            'transaction_code' => ['nullable', 'string', 'max:255'],
            'payment_proof' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:10240'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $student = User::findOrFail($validated['user_id']);

        // Handle proof upload
        $proofPath = null;
        if ($request->hasFile('payment_proof')) {
            $path = $request->file('payment_proof')->store('tuition/proofs', 'public');
            $proofPath = Storage::url($path);
        }

        // Determine start date and end date if not explicitly filled
        $monthsCount = (int) ($validated['months_count'] ?? 1);
        $billingType = $validated['billing_type'];

        $startDate = ! empty($validated['start_date'])
            ? Carbon::parse($validated['start_date'])
            : ($student->paid_until && $student->paid_until->isFuture() ? $student->paid_until->copy()->addDay() : now()->startOfDay());

        if ($billingType === 'full_course') {
            $endDate = null;
        } elseif (! empty($validated['end_date'])) {
            $endDate = Carbon::parse($validated['end_date']);
        } else {
            // Tự động tính ngày kết thúc
            $endDate = $startDate->copy()->addMonths($monthsCount)->subDay();
        }

        // Create payment record
        $payment = TuitionPayment::create([
            'user_id' => $student->id,
            'admin_id' => $request->user()->id,
            'course_id' => $validated['course_id'] ?? null,
            'amount' => $validated['amount'],
            'months_count' => $monthsCount,
            'billing_type' => $billingType,
            'start_date' => $startDate?->toDateString(),
            'end_date' => $endDate?->toDateString(),
            'payment_method' => $validated['payment_method'],
            'transaction_code' => $validated['transaction_code'] ?? null,
            'payment_proof' => $proofPath,
            'note' => $validated['note'] ?? null,
        ]);

        // Update student's tuition fields
        $student->billing_type = $billingType;
        if ($billingType === 'full_course') {
            $student->paid_until = null;
        } elseif ($endDate) {
            // If new end date is beyond previous paid_until, update
            if (! $student->paid_until || $endDate->gt($student->paid_until)) {
                $student->paid_until = $endDate->toDateString();
            }
        }
        $student->save();

        // Send confirmation notification to student
        $formattedAmount = number_format($payment->amount, 0, ',', '.') . ' VNĐ';
        $periodNote = $billingType === 'full_course'
            ? 'Trọn gói cả khóa học'
            : 'Từ ' . $startDate->format('d/m/Y') . ' đến ' . $endDate?->format('d/m/Y');

        UserNotification::create([
            'user_id' => $student->id,
            'title' => 'Xác nhận thu học phí thành công',
            'message' => 'Giảng viên đã xác nhận học phí của bạn: ' . $formattedAmount . ' (' . $periodNote . '). Cảm ơn bạn!',
            'type' => 'info',
            'data' => [
                'payment_id' => $payment->id,
                'amount' => $payment->amount,
                'end_date' => $endDate?->toDateString(),
            ],
            'is_read' => false,
        ]);

        return redirect()->route('admin.tuition.index')
            ->with('success', 'Đã ghi nhận đóng học phí thành công cho học viên ' . $student->name . ' (' . $formattedAmount . ')!');
    }

    /**
     * Display payment transaction history.
     */
    public function history(Request $request): View
    {
        $query = TuitionPayment::with(['student', 'admin', 'course'])->latest();

        if ($request->filled('student_id')) {
            $query->where('user_id', $request->input('student_id'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhere('transaction_code', 'like', "%{$search}%");
        }

        $payments = $query->paginate(20)->withQueryString();
        $totalAmount = (clone $query)->sum('amount');

        return view('admin.tuition.history', compact('payments', 'totalAmount'));
    }

    /**
     * Display detailed payment history for a single student.
     */
    public function studentHistory(User $student): View
    {
        $payments = $student->tuitionPayments()->with(['admin', 'course'])->paginate(15);
        $totalPaid = $student->tuitionPayments()->sum('amount');

        return view('admin.tuition.student-history', compact('student', 'payments', 'totalPaid'));
    }

    /**
     * Send 1-click tuition reminder notification to student.
     */
    public function remind(Request $request, User $student): RedirectResponse
    {
        $statusInfo = $student->tuition_status;
        $overdueText = $statusInfo['description'] ?? 'Đã đến hạn nộp học phí';

        UserNotification::create([
            'user_id' => $student->id,
            'title' => 'Nhắc nhở nộp học phí khóa học',
            'message' => 'Xin chào ' . $student->name . ', tài khoản của bạn đang có thông báo: ' . $overdueText . '. Vui lòng chuyển khoản học phí duy trì để tiếp tục quá trình học tập.',
            'type' => 'announcement',
            'data' => [
                'type' => 'tuition_reminder',
                'student_id' => $student->id,
                'paid_until' => $student->paid_until?->toDateString(),
            ],
            'is_read' => false,
        ]);

        return back()->with('success', 'Đã gửi thông báo nhắc học phí thành công tới học viên ' . $student->name . '!');
    }
}

