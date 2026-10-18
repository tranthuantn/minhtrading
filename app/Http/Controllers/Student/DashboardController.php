<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\TradeEntry;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show student dashboard with summary metrics and recent activity.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        // 1. Unread notifications count
        $unreadNotificationsCount = UserNotification::where('user_id', $userId)
            ->unread()
            ->count();

        // 2. Pending assignments (active assignments where student has not submitted or needs resubmission)
        $submittedAssignmentIds = Submission::where('user_id', $userId)->pluck('assignment_id');
        $pendingAssignments = Assignment::with(['lesson.course'])
            ->active()
            ->whereNotIn('id', $submittedAssignmentIds)
            ->orderBy('due_date', 'asc')
            ->take(5)
            ->get();

        // 3. Recent trade entries (latest 5)
        $recentTrades = TradeEntry::where('user_id', $userId)
            ->latest('entry_time')
            ->take(5)
            ->get();

        // 4. Quick stats
        $trades = TradeEntry::where('user_id', $userId)->get();
        $totalTrades = $trades->count();
        $wins = $trades->where('status', 'win')->count();
        $losses = $trades->where('status', 'loss')->count();
        $closedTrades = $wins + $losses;
        $winRate = $closedTrades > 0 ? round(($wins / $closedTrades) * 100, 1) : 0;
        $totalPnl = round((float) $trades->sum('pnl'), 2);

        // 5. Recent submissions with review status
        $recentSubmissions = Submission::with(['assignment', 'latestReview'])
            ->where('user_id', $userId)
            ->latest('submitted_at')
            ->take(5)
            ->get();

        return view('student.dashboard', compact(
            'unreadNotificationsCount',
            'pendingAssignments',
            'recentTrades',
            'totalTrades',
            'winRate',
            'totalPnl',
            'recentSubmissions'
        ));
    }
}
