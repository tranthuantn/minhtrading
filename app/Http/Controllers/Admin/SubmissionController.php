<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Submission;
use App\Models\SubmissionReview;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Display a listing of submissions with filters and pagination.
     */
    public function index(Request $request): View
    {
        $query = Submission::with(['assignment.lesson.course', 'student', 'latestReview.reviewer']);

        if ($request->filled('course_id')) {
            $courseId = $request->input('course_id');
            $query->whereHas('assignment.lesson', function ($q) use ($courseId) {
                $q->where('course_id', $courseId);
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('student_id')) {
            $query->where('user_id', $request->input('student_id'));
        }

        if ($request->filled('assignment_id')) {
            $query->where('assignment_id', $request->input('assignment_id'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->whereHas('student', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })->orWhereHas('assignment', function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%");
            });
        }

        $submissions = $query->latest('submitted_at')->paginate(15)->withQueryString();
        $courses = Course::orderBy('sort_order')->get();
        $assignments = Assignment::orderBy('title')->get();
        $students = User::students()->orderBy('name')->get();

        return view('admin.submissions.index', compact('submissions', 'assignments', 'students', 'courses'));
    }

    /**
     * Display the specified submission detail.
     */
    public function show(int $id): View
    {
        $submission = Submission::with([
            'assignment.lesson.course',
            'student',
            'reviews.reviewer',
        ])->findOrFail($id);

        return view('admin.submissions.show', compact('submission'));
    }

    /**
     * Show the form for reviewing the submission.
     */
    public function review(int $id): View
    {
        $submission = Submission::with([
            'assignment.lesson.course',
            'student',
            'latestReview',
        ])->findOrFail($id);

        return view('admin.submissions.review', compact('submission'));
    }

    /**
     * Store a newly created review in storage.
     */
    public function storeReview(Request $request, int $id): RedirectResponse
    {
        $submission = Submission::with('assignment')->findOrFail($id);

        $maxPoints = $submission->assignment->points ?? 100;

        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0'],
            'feedback' => ['required', 'string'],
            'corrections' => ['nullable', 'string'],
            'status' => ['required', 'in:approved,rejected,reviewed,revision_needed'],
        ]);

        DB::transaction(function () use ($submission, $validated, $request) {
            $studentId = $submission->student_id ?? $submission->user_id;
            $newStatus = $validated['status'] === 'rejected' ? 'revision_needed' : $validated['status'];

            // Create review entry
            SubmissionReview::create([
                'submission_id' => $submission->id,
                'reviewer_id' => $request->user()->id,
                'score' => $validated['score'],
                'feedback' => $validated['feedback'],
                'corrections' => $validated['corrections'] ?? null,
                'status' => $newStatus,
                'reviewed_at' => now(),
            ]);

            // Update submission status, score and featured evaluation
            $isFeatured = $request->boolean('is_featured');
            $submission->update([
                'score' => $validated['score'],
                'status' => $newStatus,
                'is_featured' => $isFeatured,
                'featured_note' => $isFeatured ? $request->input('featured_note') : null,
                'featured_at' => $isFeatured ? now() : null,
                'student_id' => $studentId,
                'user_id' => $studentId,
            ]);

            // Send notification to the student
            if ($studentId) {
                $featuredText = $isFeatured ? ' Đặc biệt, bài làm của bạn đã được Giảng viên đánh giá là BÀI TẬP TIÊU BIỂU của lớp!' : '';
                UserNotification::create([
                    'user_id' => $studentId,
                    'title' => 'Bài nộp đã được chấm: ' . ($submission->assignment->title ?? 'Bài tập'),
                    'message' => 'Giảng viên đã đánh giá bài làm của bạn với số điểm: ' . $validated['score'] . ' điểm.' . $featuredText,
                    'type' => 'assignment',
                    'data' => [
                        'submission_id' => $submission->id,
                        'assignment_id' => $submission->assignment_id,
                        'score' => $validated['score'],
                        'status' => $newStatus,
                        'is_featured' => $isFeatured,
                    ],
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('admin.submissions.show', $submission->id)
            ->with('success', 'Đã lưu đánh giá bài nộp và cập nhật trạng thái tiêu biểu thành công.');
    }

    /**
     * Toggle featured status of a submission.
     */
    public function toggleFeatured(Request $request, int $id): RedirectResponse
    {
        $submission = Submission::with(['student', 'assignment'])->findOrFail($id);

        $newFeatured = ! $submission->is_featured;
        $submission->update([
            'is_featured' => $newFeatured,
            'featured_note' => $newFeatured ? ($request->input('featured_note') ?? $submission->featured_note) : null,
            'featured_at' => $newFeatured ? now() : null,
        ]);

        if ($newFeatured && $submission->student_id) {
            UserNotification::create([
                'user_id' => $submission->student_id,
                'title' => '🌟 Bài tập của bạn được chọn làm Bài Tập Tiêu Biểu!',
                'message' => 'Bài làm của bạn cho bài tập "' . ($submission->assignment->title ?? 'Bài tập') . '" đã được Giảng viên bình chọn là Bài Tập Tiêu Biểu trong thư viện học tập.',
                'type' => 'announcement',
                'data' => [
                    'submission_id' => $submission->id,
                    'assignment_id' => $submission->assignment_id,
                ],
                'is_read' => false,
            ]);
        }

        $message = $newFeatured 
            ? 'Đã gắn nhãn Bài Tập Tiêu Biểu cho bài nộp của học viên ' . ($submission->student->name ?? '') . '.'
            : 'Đã hủy nhãn Tiêu Biểu cho bài nộp này.';

        return back()->with('success', $message);
    }

    /**
     * Bulk review (approve or reject) multiple submissions.
     */
    public function bulkReview(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'submission_ids' => ['required', 'array'],
            'submission_ids.*' => ['exists:submissions,id'],
            'action' => ['required', 'in:approved,rejected'],
            'feedback' => ['nullable', 'string'],
            'score' => ['nullable', 'numeric', 'min:0'],
        ]);

        $submissions = Submission::with('assignment')->whereIn('id', $validated['submission_ids'])->get();

        DB::transaction(function () use ($submissions, $validated, $request) {
            foreach ($submissions as $submission) {
                $score = $validated['score'] ?? ($validated['action'] === 'approved' ? ($submission->assignment->points ?? 100) : 0);

                SubmissionReview::create([
                    'submission_id' => $submission->id,
                    'reviewer_id' => $request->user()->id,
                    'score' => $score,
                    'feedback' => $validated['feedback'] ?? ($validated['action'] === 'approved' ? 'Đã duyệt đạt yêu cầu.' : 'Chưa đạt yêu cầu, vui lòng xem lại.'),
                    'status' => $validated['action'],
                    'reviewed_at' => now(),
                ]);

                $submission->update([
                    'score' => $score,
                    'status' => $validated['action'],
                ]);

                UserNotification::create([
                    'user_id' => $submission->user_id,
                    'title' => 'Bài nộp được cập nhật: ' . $submission->assignment->title,
                    'message' => 'Bài tập của bạn đã được ' . ($validated['action'] === 'approved' ? 'duyệt đạt' : 'đánh giá lại') . '.',
                    'type' => 'assignment',
                    'data' => [
                        'submission_id' => $submission->id,
                        'assignment_id' => $submission->assignment_id,
                    ],
                    'is_read' => false,
                ]);
            }
        });

        return redirect()->route('admin.submissions.index')
            ->with('success', 'Đã xử lý hàng loạt ' . count($submissions) . ' bài nộp thành công.');
    }
}
