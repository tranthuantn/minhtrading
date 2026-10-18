<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TradeEntry;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TradeStatController extends Controller
{
    /**
     * Overview of all trading statistics in the system.
     */
    public function index(Request $request): View|JsonResponse
    {
        $tradesQuery = TradeEntry::query();

        if ($request->filled('type')) {
            $tradesQuery->where('trade_type', $request->input('type'));
        }

        if ($request->filled('pair')) {
            $tradesQuery->where('pair', strtoupper($request->input('pair')));
        }

        $allTrades = (clone $tradesQuery)->orderBy('entry_time', 'asc')->get();

        $totalTrades = $allTrades->count();
        $winCount = $allTrades->where('status', 'win')->count();
        $lossCount = $allTrades->where('status', 'loss')->count();
        $breakevenCount = $allTrades->where('status', 'breakeven')->count();
        $openCount = $allTrades->where('status', 'open')->count();
        $closedTrades = $winCount + $lossCount;
        $winRate = $closedTrades > 0 ? round(($winCount / $closedTrades) * 100, 2) : 0;
        $totalPnl = round((float) $allTrades->sum('pnl'), 2);
        $avgRr = round((float) ($allTrades->whereNotNull('rr_ratio')->avg('rr_ratio') ?? 0), 2);

        // Group by pair
        $pairStats = $allTrades->groupBy('pair')->map(function ($items, $pair) {
            $wins = $items->where('status', 'win')->count();
            $losses = $items->where('status', 'loss')->count();
            $total = $items->count();
            $rate = ($wins + $losses) > 0 ? round(($wins / ($wins + $losses)) * 100, 1) : 0;

            return [
                'pair' => $pair,
                'count' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'win_rate' => $rate,
                'pnl' => round((float) $items->sum('pnl'), 2),
            ];
        })->sortByDesc('count')->values();

        // Cumulative P&L over time
        $cumulative = 0;
        $pnlCurveLabels = [];
        $pnlCurveData = [];
        foreach ($allTrades as $idx => $trade) {
            $cumulative += (float) ($trade->pnl ?? 0);
            $pnlCurveLabels[] = $trade->entry_time ? $trade->entry_time->format('d/m/Y') : '#' . ($idx + 1);
            $pnlCurveData[] = round($cumulative, 2);
        }

        // Top students by total P&L
        $studentsList = User::students()->active()->with('tradeEntries')->get();
        $topStudents = $studentsList->map(function ($user) {
            $trades = $user->tradeEntries;
            $wins = $trades->where('status', 'win')->count();
            $losses = $trades->where('status', 'loss')->count();
            $total = $trades->count();
            $wr = ($wins + $losses) > 0 ? round(($wins / ($wins + $losses)) * 100, 1) : 0;

            return [
                'id' => $user->id,
                'name' => $user->name,
                'trades_count' => $total,
                'win_rate' => $wr,
                'total_pnl' => round((float) $trades->sum('pnl'), 2),
            ];
        })->filter(fn ($s) => $s['trades_count'] > 0)
          ->sortByDesc('total_pnl')
          ->take(10)
          ->values();

        $chartData = [
            'pnl_curve' => [
                'labels' => $pnlCurveLabels,
                'datasets' => [
                    [
                        'label' => 'Tổng P&L toàn hệ thống ($)',
                        'data' => $pnlCurveData,
                        'borderColor' => '#3B82F6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.2,
                    ],
                ],
            ],
            'pair_distribution' => [
                'labels' => $pairStats->pluck('pair')->take(8)->values(),
                'datasets' => [
                    [
                        'label' => 'Số lượng lệnh',
                        'data' => $pairStats->pluck('count')->take(8)->values(),
                        'backgroundColor' => ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1', '#14B8A6'],
                    ],
                ],
            ],
            'win_loss_doughnut' => [
                'labels' => ['Thắng', 'Thua', 'Hòa'],
                'datasets' => [
                    [
                        'data' => [$winCount, $lossCount, $breakevenCount],
                        'backgroundColor' => ['#10B981', '#EF4444', '#9CA3AF'],
                    ],
                ],
            ],
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'summary' => [
                    'total_trades' => $totalTrades,
                    'win_count' => $winCount,
                    'loss_count' => $lossCount,
                    'win_rate' => $winRate,
                    'total_pnl' => $totalPnl,
                    'avg_rr' => $avgRr,
                ],
                'pair_stats' => $pairStats,
                'charts' => $chartData,
            ]);
        }

        $students = User::students()->orderBy('name')->get();

        return view('admin.trade-stats.index', compact(
            'totalTrades',
            'winCount',
            'lossCount',
            'breakevenCount',
            'winRate',
            'totalPnl',
            'avgRr',
            'pairStats',
            'topStudents',
            'chartData',
            'students'
        ));
    }

    /**
     * Trading statistics for a specific student with charts data.
     */
    public function studentStats(Request $request, User|int $student): View|JsonResponse
    {
        $student = $student instanceof User ? $student : User::students()->findOrFail($student);

        $trades = TradeEntry::where('user_id', $student->id)
            ->orderBy('entry_time', 'asc')
            ->get();

        $totalTrades = $trades->count();
        $winCount = $trades->where('status', 'win')->count();
        $lossCount = $trades->where('status', 'loss')->count();
        $breakevenCount = $trades->where('status', 'breakeven')->count();
        $closedTrades = $winCount + $lossCount;
        $winRate = $closedTrades > 0 ? round(($winCount / $closedTrades) * 100, 2) : 0;
        $totalPnl = round((float) $trades->sum('pnl'), 2);

        // Drawdown & P&L Curve calculation
        $runningPnl = 0;
        $peakEquity = 0;
        $maxDrawdown = 0;
        $pnlCurveLabels = [];
        $pnlCurveData = [];
        $drawdownData = [];
        $rollingWinRateLabels = [];
        $rollingWinRateData = [];

        $accumWins = 0;
        $accumClosed = 0;

        foreach ($trades as $index => $trade) {
            $tradePnl = (float) ($trade->pnl ?? 0);
            $runningPnl += $tradePnl;

            if ($runningPnl > $peakEquity) {
                $peakEquity = $runningPnl;
            }
            $drawdown = $peakEquity - $runningPnl;
            if ($drawdown > $maxDrawdown) {
                $maxDrawdown = $drawdown;
            }

            if ($trade->status === 'win') {
                $accumWins++;
                $accumClosed++;
            } elseif ($trade->status === 'loss') {
                $accumClosed++;
            }

            $currentWinRate = $accumClosed > 0 ? round(($accumWins / $accumClosed) * 100, 1) : 0;
            $label = $trade->entry_time ? $trade->entry_time->format('d/m/Y') : '#' . ($index + 1);

            $pnlCurveLabels[] = $label;
            $pnlCurveData[] = round($runningPnl, 2);
            $drawdownData[] = round(-$drawdown, 2); // negative value for drawdown chart
            $rollingWinRateLabels[] = $label;
            $rollingWinRateData[] = $currentWinRate;
        }

        // By currency pair stats
        $pairStats = $trades->groupBy('pair')->map(function ($items, $pair) {
            $wins = $items->where('status', 'win')->count();
            $losses = $items->where('status', 'loss')->count();
            $total = $items->count();
            $wr = ($wins + $losses) > 0 ? round(($wins / ($wins + $losses)) * 100, 1) : 0;

            return [
                'pair' => $pair,
                'total' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'win_rate' => $wr,
                'pnl' => round((float) $items->sum('pnl'), 2),
            ];
        })->sortByDesc('total')->values();

        // Chart.js JSON structure
        $chartData = [
            'win_rate_over_time' => [
                'labels' => $rollingWinRateLabels,
                'datasets' => [
                    [
                        'label' => 'Tỷ lệ thắng tích lũy (%)',
                        'data' => $rollingWinRateData,
                        'borderColor' => '#8B5CF6',
                        'backgroundColor' => 'rgba(139, 92, 246, 0.1)',
                        'fill' => false,
                        'tension' => 0.2,
                    ],
                ],
            ],
            'pnl_curve' => [
                'labels' => $pnlCurveLabels,
                'datasets' => [
                    [
                        'label' => 'Đường cong vốn P&L ($)',
                        'data' => $pnlCurveData,
                        'borderColor' => '#10B981',
                        'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                        'fill' => true,
                        'tension' => 0.2,
                    ],
                ],
            ],
            'drawdown' => [
                'labels' => $pnlCurveLabels,
                'datasets' => [
                    [
                        'label' => 'Mức sụt giảm vốn (Drawdown $)',
                        'data' => $drawdownData,
                        'borderColor' => '#EF4444',
                        'backgroundColor' => 'rgba(239, 68, 68, 0.2)',
                        'fill' => true,
                        'tension' => 0.2,
                    ],
                ],
            ],
            'pair_distribution' => [
                'labels' => $pairStats->pluck('pair')->take(6)->values(),
                'datasets' => [
                    [
                        'label' => 'P&L theo cặp tiền ($)',
                        'data' => $pairStats->pluck('pnl')->take(6)->values(),
                        'backgroundColor' => $pairStats->pluck('pnl')->take(6)->map(fn ($val) => $val >= 0 ? '#10B981' : '#EF4444')->values(),
                    ],
                ],
            ],
        ];

        $summary = [
            'total_trades' => $totalTrades,
            'win_count' => $winCount,
            'loss_count' => $lossCount,
            'breakeven_count' => $breakevenCount,
            'win_rate' => $winRate,
            'total_pnl' => $totalPnl,
            'max_drawdown' => round($maxDrawdown, 2),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'student' => [
                    'id' => $student->id,
                    'name' => $student->name,
                    'email' => $student->email,
                ],
                'summary' => $summary,
                'pair_stats' => $pairStats,
                'charts' => $chartData,
            ]);
        }

        return view('admin.trade-stats.student', compact('student', 'summary', 'pairStats', 'chartData'));
    }
}
