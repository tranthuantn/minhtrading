<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications for the logged-in student.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $query = UserNotification::where('user_id', $userId);

        if ($request->filled('filter')) {
            $filter = $request->input('filter');
            if ($filter === 'unread') {
                $query->unread();
            } elseif ($filter === 'read') {
                $query->read();
            }
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $notifications = $query->latest()->paginate(15)->withQueryString();
        $unreadCount = UserNotification::where('user_id', $userId)->unread()->count();

        return view('student.notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Display the specified notification and mark it as read.
     */
    public function show(UserNotification|int $notification): View
    {
        $notification = $notification instanceof UserNotification
            ? $notification
            : UserNotification::where('user_id', auth()->id())->findOrFail($notification);

        abort_if($notification->user_id !== auth()->id(), 403, 'Bạn không có quyền truy cập thông báo này.');

        if (! $notification->is_read) {
            $notification->markAsRead();
        }

        return view('student.notifications.show', compact('notification'));
    }

    /**
     * Mark all notifications as read for the logged-in student.
     */
    public function markAllRead(Request $request): RedirectResponse
    {
        UserNotification::where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }
}
