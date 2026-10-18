<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Submission;
use App\Models\TradeEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Show admin dashboard with statistics and recent activity.
     */
    public function index(): View
    {
        // 1. Total active students
        $totalStudents = User::students()->active()->count();

        // 2. Total courses
        $totalCourses = Course::count();

        // 3. Submissions pending review
        $pendingSubmissionsCount = Submission::pending()->count();

        // 4. Trade entries this month
        $tradeEntriesThisMonth = TradeEntry::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // 5. Recent submissions needing review (latest 5)
        $recentSubmissions = Submission::with(['student', 'assignment.lesson.course'])
            ->pending()
            ->latest('submitted_at')
            ->take(5)
            ->get();

        // 6. Student performance overview (top 5 by win rate with minimum trades)
        $students = User::students()
            ->active()
            ->with(['tradeEntries' => function ($query) {
                $query->whereIn('status', ['win', 'loss']);
            }])
            ->get();

        $topStudents = $students->map(function ($student) {
            $totalTrades = $student->tradeEntries->count();
            $wins = $student->tradeEntries->where('status', 'win')->count();
            $losses = $student->tradeEntries->where('status', 'loss')->count();
            $totalPnl = $student->tradeEntries->sum('pnl');
            $winRate = $totalTrades > 0 ? round(($wins / $totalTrades) * 100, 2) : 0;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'email' => $student->email,
                'avatar' => $student->avatar,
                'total_trades' => $totalTrades,
                'wins' => $wins,
                'losses' => $losses,
                'win_rate' => $winRate,
                'total_pnl' => $totalPnl,
            ];
        })
        ->filter(fn ($item) => $item['total_trades'] > 0)
        ->sortByDesc('win_rate')
        ->take(5)
        ->values();

        return view('admin.dashboard', compact(
            'totalStudents',
            'totalCourses',
            'pendingSubmissionsCount',
            'tradeEntriesThisMonth',
            'recentSubmissions',
            'topStudents'
        ));
    }
}
