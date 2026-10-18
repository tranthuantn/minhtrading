<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of sent notifications.
     */
    public function index(Request $request): View
    {
        $query = UserNotification::with('user');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('student_id')) {
            $query->where('user_id', $request->input('student_id'));
        }

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();
        $students = User::students()->orderBy('name')->get();

        return view('admin.notifications.index', compact('notifications', 'students'));
    }

    /**
     * Show the form for creating a new notification.
     */
    public function create(): View
    {
        $students = User::students()->active()->orderBy('name')->get();

        return view('admin.notifications.create', compact('students'));
    }

    /**
     * Store a newly created notification (send to all or selected students).
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'type' => ['required', 'in:info,warning,success,danger,assignment,trade'],
            'recipient_type' => ['required', 'in:all,selected'],
            'student_ids' => ['required_if:recipient_type,selected', 'array'],
            'student_ids.*' => ['exists:users,id'],
        ]);

        $recipientIds = [];
        if ($validated['recipient_type'] === 'all') {
            $recipientIds = User::students()->active()->pluck('id')->toArray();
        } else {
            $recipientIds = $validated['student_ids'] ?? [];
        }

        if (empty($recipientIds)) {
            return back()->withErrors(['recipient_type' => 'Không tìm thấy học viên nào để gửi thông báo.'])->withInput();
        }

        DB::transaction(function () use ($recipientIds, $validated) {
            $now = now();
            $records = [];
            foreach ($recipientIds as $studentId) {
                $records[] = [
                    'user_id' => $studentId,
                    'title' => $validated['title'],
                    'message' => $validated['message'],
                    'type' => $validated['type'],
                    'data' => json_encode(['sender' => 'admin']),
                    'is_read' => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Chunk insert for performance
            foreach (array_chunk($records, 100) as $chunk) {
                UserNotification::insert($chunk);
            }
        });

        $count = count($recipientIds);

        return redirect()->route('admin.notifications.index')
            ->with('success', "Đã gửi thông báo thành công đến {$count} học viên.");
    }

    /**
     * Remove the specified notification from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $notification = UserNotification::findOrFail($id);
        $notification->delete();

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Đã xóa thông báo thành công.');
    }
}
