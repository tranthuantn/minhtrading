<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TradeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BacktestController extends Controller
{
    /**
     * Display a listing of student's backtest entries with stats summary.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $query = TradeEntry::where('user_id', $userId)
            ->where('trade_type', 'backtest');

        if ($request->filled('pair')) {
            $query->where('pair', strtoupper($request->input('pair')));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        // Summary metrics for backtest
        $allBacktests = TradeEntry::where('user_id', $userId)
            ->where('trade_type', 'backtest')
            ->get();

        $totalCount = $allBacktests->count();
        $wins = $allBacktests->where('status', 'win')->count();
        $losses = $allBacktests->where('status', 'loss')->count();
        $closed = $wins + $losses;
        $winRate = $closed > 0 ? round(($wins / $closed) * 100, 1) : 0;
        $totalPnl = round((float) $allBacktests->sum('pnl'), 2);
        $avgRr = round((float) ($allBacktests->whereNotNull('rr_ratio')->avg('rr_ratio') ?? 0), 2);

        $summary = [
            'total' => $totalCount,
            'wins' => $wins,
            'losses' => $losses,
            'win_rate' => $winRate,
            'total_pnl' => $totalPnl,
            'avg_rr' => $avgRr,
        ];

        $backtests = $query->latest('entry_time')->paginate(15)->withQueryString();

        return view('student.backtests.index', compact('backtests', 'summary'));
    }

    /**
     * Show the form for creating a new backtest entry.
     */
    public function create(): View
    {
        return view('student.backtests.create');
    }

    /**
     * Store a newly created backtest in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'pnl' => ['nullable', 'numeric'],
            'pnl_pips' => ['nullable', 'numeric'],
            'rr_ratio' => ['nullable', 'numeric'],
            'status' => ['required', 'in:win,loss,breakeven,open,cancelled'],
            'entry_time' => ['required', 'date'],
            'exit_time' => ['nullable', 'date'],
            'setup_description' => ['nullable', 'string'],
            'chart_before' => ['nullable', 'image', 'max:4096'],
            'chart_before_url' => ['nullable', 'url', 'max:500'],
            'chart_after' => ['nullable', 'image', 'max:4096'],
            'chart_after_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string'],
            'lessons_learned' => ['nullable', 'string'],
        ]);

        $beforePath = null;
        if ($request->hasFile('chart_before')) {
            $beforePath = $request->file('chart_before')->store('backtests/charts', 'public');
        } elseif (! empty($validated['chart_before_url'])) {
            $beforePath = $validated['chart_before_url'];
        }

        $afterPath = null;
        if ($request->hasFile('chart_after')) {
            $afterPath = $request->file('chart_after')->store('backtests/charts', 'public');
        } elseif (! empty($validated['chart_after_url'])) {
            $afterPath = $validated['chart_after_url'];
        }

        // Calculate R:R ratio if not provided
        $rrRatio = $validated['rr_ratio'] ?? null;
        if ($rrRatio === null && ! empty($validated['entry_price']) && ! empty($validated['stop_loss']) && ! empty($validated['take_profit'])) {
            $risk = abs($validated['entry_price'] - $validated['stop_loss']);
            $reward = abs($validated['take_profit'] - $validated['entry_price']);
            if ($risk > 0) {
                $rrRatio = round($reward / $risk, 2);
            }
        }

        $trade = TradeEntry::create([
            'user_id' => $request->user()->id,
            'pair' => strtoupper($validated['pair']),
            'timeframe' => $validated['timeframe'],
            'direction' => $validated['direction'],
            'trade_type' => 'backtest',
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'pnl' => $validated['pnl'] ?? 0,
            'pnl_pips' => $validated['pnl_pips'] ?? null,
            'rr_ratio' => $rrRatio,
            'status' => $validated['status'],
            'entry_time' => $validated['entry_time'],
            'exit_time' => $validated['exit_time'] ?? null,
            'setup_description' => $validated['setup_description'] ?? null,
            'chart_before_url' => $beforePath,
            'chart_after_url' => $afterPath,
            'notes' => $validated['notes'] ?? null,
            'lessons_learned' => $validated['lessons_learned'] ?? null,
        ]);

        return redirect()->route('student.backtests.show', $trade->id)
            ->with('success', 'Đã lưu nhật ký backtest thành công.');
    }

    /**
     * Display the specified backtest detail.
     */
    public function show(int $id): View
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->where('trade_type', 'backtest')
            ->findOrFail($id);

        return view('student.backtests.show', compact('trade'));
    }

    /**
     * Show the form for editing the backtest entry.
     */
    public function edit(int $id): View
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->where('trade_type', 'backtest')
            ->findOrFail($id);

        return view('student.backtests.edit', compact('trade'));
    }

    /**
     * Update the specified backtest in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->where('trade_type', 'backtest')
            ->findOrFail($id);

        $validated = $request->validate([
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'pnl' => ['nullable', 'numeric'],
            'pnl_pips' => ['nullable', 'numeric'],
            'rr_ratio' => ['nullable', 'numeric'],
            'status' => ['required', 'in:win,loss,breakeven,open,cancelled'],
            'entry_time' => ['required', 'date'],
            'exit_time' => ['nullable', 'date'],
            'setup_description' => ['nullable', 'string'],
            'chart_before' => ['nullable', 'image', 'max:4096'],
            'chart_before_url' => ['nullable', 'url', 'max:500'],
            'chart_after' => ['nullable', 'image', 'max:4096'],
            'chart_after_url' => ['nullable', 'url', 'max:500'],
            'notes' => ['nullable', 'string'],
            'lessons_learned' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('chart_before')) {
            if ($trade->chart_before_url && Storage::disk('public')->exists($trade->chart_before_url)) {
                Storage::disk('public')->delete($trade->chart_before_url);
            }
            $trade->chart_before_url = $request->file('chart_before')->store('backtests/charts', 'public');
        } elseif (! empty($validated['chart_before_url'])) {
            $trade->chart_before_url = $validated['chart_before_url'];
        }

        if ($request->hasFile('chart_after')) {
            if ($trade->chart_after_url && Storage::disk('public')->exists($trade->chart_after_url)) {
                Storage::disk('public')->delete($trade->chart_after_url);
            }
            $trade->chart_after_url = $request->file('chart_after')->store('backtests/charts', 'public');
        } elseif (! empty($validated['chart_after_url'])) {
            $trade->chart_after_url = $validated['chart_after_url'];
        }

        $rrRatio = $validated['rr_ratio'] ?? $trade->rr_ratio;
        if ($rrRatio === null && ! empty($validated['entry_price']) && ! empty($validated['stop_loss']) && ! empty($validated['take_profit'])) {
            $risk = abs($validated['entry_price'] - $validated['stop_loss']);
            $reward = abs($validated['take_profit'] - $validated['entry_price']);
            if ($risk > 0) {
                $rrRatio = round($reward / $risk, 2);
            }
        }

        $trade->update([
            'pair' => strtoupper($validated['pair']),
            'timeframe' => $validated['timeframe'],
            'direction' => $validated['direction'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'pnl' => $validated['pnl'] ?? $trade->pnl,
            'pnl_pips' => $validated['pnl_pips'] ?? null,
            'rr_ratio' => $rrRatio,
            'status' => $validated['status'],
            'entry_time' => $validated['entry_time'],
            'exit_time' => $validated['exit_time'] ?? null,
            'setup_description' => $validated['setup_description'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'lessons_learned' => $validated['lessons_learned'] ?? null,
        ]);

        return redirect()->route('student.backtests.show', $trade->id)
            ->with('success', 'Đã cập nhật lệnh backtest thành công.');
    }

    /**
     * Remove the specified backtest from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->where('trade_type', 'backtest')
            ->findOrFail($id);

        if ($trade->chart_before_url && Storage::disk('public')->exists($trade->chart_before_url)) {
            Storage::disk('public')->delete($trade->chart_before_url);
        }
        if ($trade->chart_after_url && Storage::disk('public')->exists($trade->chart_after_url)) {
            Storage::disk('public')->delete($trade->chart_after_url);
        }

        $trade->delete();

        return redirect()->route('student.backtests.index')
            ->with('success', 'Đã xóa lệnh backtest thành công.');
    }

    /**
     * Personal backtest statistics with chart data (JSON or View).
     */
    public function stats(Request $request): View|JsonResponse
    {
        $trades = TradeEntry::where('user_id', auth()->id())
            ->where('trade_type', 'backtest')
            ->orderBy('entry_time', 'asc')
            ->get();

        $total = $trades->count();
        $wins = $trades->where('status', 'win')->count();
        $losses = $trades->where('status', 'loss')->count();
        $breakevens = $trades->where('status', 'breakeven')->count();
        $closed = $wins + $losses;
        $winRate = $closed > 0 ? round(($wins / $closed) * 100, 1) : 0;
        $totalPnl = round((float) $trades->sum('pnl'), 2);

        // Cumulative P&L curve
        $pnlCurveLabels = [];
        $pnlCurveData = [];
        $runningPnl = 0;
        foreach ($trades as $idx => $t) {
            $runningPnl += (float) ($t->pnl ?? 0);
            $pnlCurveLabels[] = $t->entry_time ? $t->entry_time->format('d/m/Y') : '#' . ($idx + 1);
            $pnlCurveData[] = round($runningPnl, 2);
        }

        // Pair breakdown
        $pairStats = $trades->groupBy('pair')->map(function ($items, $pair) {
            $w = $items->where('status', 'win')->count();
            $l = $items->where('status', 'loss')->count();
            $tot = $items->count();

            return [
                'pair' => $pair,
                'count' => $tot,
                'wins' => $w,
                'losses' => $l,
                'win_rate' => ($w + $l) > 0 ? round(($w / ($w + $l)) * 100, 1) : 0,
                'pnl' => round((float) $items->sum('pnl'), 2),
            ];
        })->sortByDesc('count')->values();

        $chartData = [
            'pnl_curve' => [
                'labels' => $pnlCurveLabels,
                'datasets' => [
                    [
                        'label' => 'Đường cong vốn Backtest ($)',
                        'data' => $pnlCurveData,
                        'borderColor' => '#3B82F6',
                        'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                        'fill' => true,
                        'tension' => 0.2,
                    ],
                ],
            ],
            'win_loss_chart' => [
                'labels' => ['Thắng', 'Thua', 'Hòa'],
                'datasets' => [
                    [
                        'data' => [$wins, $losses, $breakevens],
                        'backgroundColor' => ['#10B981', '#EF4444', '#9CA3AF'],
                    ],
                ],
            ],
            'pair_distribution' => [
                'labels' => $pairStats->pluck('pair')->take(6)->values(),
                'datasets' => [
                    [
                        'label' => 'P&L theo cặp tiền ($)',
                        'data' => $pairStats->pluck('pnl')->take(6)->values(),
                        'backgroundColor' => $pairStats->pluck('pnl')->take(6)->map(fn ($p) => $p >= 0 ? '#10B981' : '#EF4444')->values(),
                    ],
                ],
            ],
        ];

        $summary = [
            'total' => $total,
            'wins' => $wins,
            'losses' => $losses,
            'breakevens' => $breakevens,
            'win_rate' => $winRate,
            'total_pnl' => $totalPnl,
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'summary' => $summary,
                'pair_stats' => $pairStats,
                'charts' => $chartData,
            ]);
        }

        return view('student.backtests.stats', compact('summary', 'pairStats', 'chartData'));
    }
}
