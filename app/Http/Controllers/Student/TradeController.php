<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\TradeEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TradeController extends Controller
{
    /**
     * Display a listing of student's live and demo trade entries with stats.
     */
    public function index(Request $request): View
    {
        $userId = $request->user()->id;

        $query = TradeEntry::where('user_id', $userId)
            ->whereIn('trade_type', ['live', 'demo']);

        if ($request->filled('type')) {
            $query->where('trade_type', $request->input('type'));
        }

        if ($request->filled('pair')) {
            $query->where('pair', strtoupper($request->input('pair')));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        // Summary for live/demo trades
        $allTrades = TradeEntry::where('user_id', $userId)
            ->whereIn('trade_type', ['live', 'demo'])
            ->get();

        $totalTrades = $allTrades->count();
        $winCount = $allTrades->where('status', 'win')->count();
        $lossCount = $allTrades->where('status', 'loss')->count();
        $breakevenCount = $allTrades->where('status', 'breakeven')->count();
        $openCount = $allTrades->where('status', 'open')->count();
        $closedTrades = $winCount + $lossCount;
        $winRate = $closedTrades > 0 ? round(($winCount / $closedTrades) * 100, 1) : 0;
        $totalPnl = round((float) $allTrades->sum('pnl'), 2);
        $avgRr = round((float) ($allTrades->whereNotNull('rr_ratio')->avg('rr_ratio') ?? 0), 2);

        $summary = [
            'total' => $totalTrades,
            'wins' => $winCount,
            'losses' => $lossCount,
            'breakeven' => $breakevenCount,
            'open' => $openCount,
            'win_rate' => $winRate,
            'total_pnl' => $totalPnl,
            'avg_rr' => $avgRr,
        ];

        $trades = $query->latest('entry_time')->paginate(15)->withQueryString();

        return view('student.trades.index', compact('trades', 'summary'));
    }

    /**
     * Show the form for creating a new live/demo trade entry.
     */
    public function create(): View
    {
        return view('student.trades.create');
    }

    /**
     * Store a newly created trade entry in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Normalize inputs
        $pair = $request->input('pair', $request->input('currency_pair'));
        $tradeType = $request->input('trade_type', $request->input('type', 'live'));
        $direction = strtoupper($request->input('direction', 'BUY'));
        $status = $request->input('status', $request->input('result', 'pending'));
        $timeframe = $request->input('timeframe', 'H1');
        $entryTime = $request->input('entry_time', now()->toDateTimeString());
        $pnl = $request->input('pnl', $request->input('pnl_amount'));
        $notes = $request->input('notes', $request->input('analysis_notes'));

        $request->merge([
            'pair' => $pair,
            'currency_pair' => $pair,
            'trade_type' => $tradeType,
            'type' => $tradeType,
            'direction' => $direction,
            'status' => $status,
            'result' => $status,
            'timeframe' => $timeframe,
            'entry_time' => $entryTime,
            'pnl' => $pnl,
            'pnl_amount' => $pnl,
            'notes' => $notes,
            'analysis_notes' => $notes,
        ]);

        $validated = $request->validate([
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL,buy,sell'],
            'trade_type' => ['required', 'in:live,demo'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'lot_size' => ['nullable', 'numeric', 'min:0.01'],
            'pnl' => ['nullable', 'numeric'],
            'pnl_pips' => ['nullable', 'numeric'],
            'rr_ratio' => ['nullable', 'numeric'],
            'status' => ['required', 'in:win,loss,breakeven,open,pending,cancelled'],
            'entry_time' => ['required'],
            'exit_time' => ['nullable', 'date'],
            'setup_description' => ['nullable', 'string'],
            'chart_before' => ['nullable', 'image', 'max:4096'],
            'chart_before_url' => ['nullable', 'string', 'max:500'],
            'chart_after' => ['nullable', 'image', 'max:4096'],
            'chart_after_url' => ['nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string'],
            'lessons_learned' => ['nullable', 'string'],
        ]);

        $beforePath = null;
        if ($request->hasFile('chart_before')) {
            $beforePath = $request->file('chart_before')->store('trades/charts', 'public');
        } elseif (! empty($validated['chart_before_url'])) {
            $beforePath = $validated['chart_before_url'];
        }

        $afterPath = null;
        if ($request->hasFile('chart_after')) {
            $afterPath = $request->file('chart_after')->store('trades/charts', 'public');
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
            'currency_pair' => strtoupper($validated['pair']),
            'timeframe' => $validated['timeframe'],
            'direction' => strtolower($validated['direction']),
            'trade_type' => $validated['trade_type'],
            'type' => $validated['trade_type'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'lot_size' => $validated['lot_size'] ?? 0.1,
            'pnl' => $validated['pnl'] ?? 0,
            'pnl_amount' => $validated['pnl'] ?? 0,
            'pnl_pips' => $validated['pnl_pips'] ?? null,
            'rr_ratio' => $rrRatio,
            'status' => strtolower($validated['status']),
            'result' => strtolower($validated['status']),
            'entry_time' => $validated['entry_time'],
            'exit_time' => $validated['exit_time'] ?? null,
            'setup_description' => $validated['setup_description'] ?? null,
            'chart_before_url' => $beforePath,
            'chart_after_url' => $afterPath,
            'notes' => $validated['notes'] ?? null,
            'analysis_notes' => $validated['notes'] ?? null,
            'lessons_learned' => $validated['lessons_learned'] ?? null,
        ]);

        return redirect()->route('student.trades.index')
            ->with('success', 'Đã ghi nhận lệnh giao dịch thành công.');
    }

    /**
     * Display the specified trade entry.
     */
    public function show(int $id): View
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->whereIn('trade_type', ['live', 'demo'])
            ->findOrFail($id);

        return view('student.trades.show', compact('trade'));
    }

    /**
     * Show the form for editing the trade entry.
     */
    public function edit(int $id): View
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->whereIn('trade_type', ['live', 'demo'])
            ->findOrFail($id);

        return view('student.trades.edit', compact('trade'));
    }

    /**
     * Update the specified trade in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->whereIn('trade_type', ['live', 'demo'])
            ->findOrFail($id);

        $validated = $request->validate([
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL'],
            'trade_type' => ['required', 'in:live,demo'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'lot_size' => ['nullable', 'numeric', 'min:0.01'],
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
            $trade->chart_before_url = $request->file('chart_before')->store('trades/charts', 'public');
        } elseif (! empty($validated['chart_before_url'])) {
            $trade->chart_before_url = $validated['chart_before_url'];
        }

        if ($request->hasFile('chart_after')) {
            if ($trade->chart_after_url && Storage::disk('public')->exists($trade->chart_after_url)) {
                Storage::disk('public')->delete($trade->chart_after_url);
            }
            $trade->chart_after_url = $request->file('chart_after')->store('trades/charts', 'public');
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
            'trade_type' => $validated['trade_type'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'lot_size' => $validated['lot_size'] ?? $trade->lot_size,
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

        return redirect()->route('student.trades.show', $trade->id)
            ->with('success', 'Đã cập nhật lệnh giao dịch thành công.');
    }

    /**
     * Remove the specified trade from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $trade = TradeEntry::where('user_id', auth()->id())
            ->whereIn('trade_type', ['live', 'demo'])
            ->findOrFail($id);

        if ($trade->chart_before_url && Storage::disk('public')->exists($trade->chart_before_url)) {
            Storage::disk('public')->delete($trade->chart_before_url);
        }
        if ($trade->chart_after_url && Storage::disk('public')->exists($trade->chart_after_url)) {
            Storage::disk('public')->delete($trade->chart_after_url);
        }

        $trade->delete();

        return redirect()->route('student.trades.index')
            ->with('success', 'Đã xóa lệnh giao dịch thành công.');
    }

    /**
     * Personal trade statistics with chart data (JSON or View).
     */
    public function stats(Request $request): View|JsonResponse
    {
        $trades = TradeEntry::where('user_id', auth()->id())
            ->whereIn('trade_type', ['live', 'demo'])
            ->orderBy('entry_time', 'asc')
            ->get();

        $totalTrades = $trades->count();
        $winCount = $trades->where('status', 'win')->count();
        $lossCount = $trades->where('status', 'loss')->count();
        $breakevenCount = $trades->where('status', 'breakeven')->count();
        $closedTrades = $winCount + $lossCount;
        $winRate = $closedTrades > 0 ? round(($winCount / $closedTrades) * 100, 1) : 0;
        $totalPnl = round((float) $trades->sum('pnl'), 2);

        // Profit Factor
        $grossProfit = (float) $trades->where('pnl', '>', 0)->sum('pnl');
        $grossLoss = abs((float) $trades->where('pnl', '<', 0)->sum('pnl'));
        $profitFactor = $grossLoss > 0 ? round($grossProfit / $grossLoss, 2) : ($grossProfit > 0 ? 999 : 0);

        // Running P&L and Drawdown calculation
        $runningPnl = 0;
        $peakEquity = 0;
        $maxDrawdown = 0;
        $pnlCurveLabels = [];
        $pnlCurveData = [];
        $drawdownData = [];

        foreach ($trades as $idx => $t) {
            $pnl = (float) ($t->pnl ?? 0);
            $runningPnl += $pnl;
            if ($runningPnl > $peakEquity) {
                $peakEquity = $runningPnl;
            }
            $dd = $peakEquity - $runningPnl;
            if ($dd > $maxDrawdown) {
                $maxDrawdown = $dd;
            }

            $label = $t->entry_time ? $t->entry_time->format('d/m/Y') : '#' . ($idx + 1);
            $pnlCurveLabels[] = $label;
            $pnlCurveData[] = round($runningPnl, 2);
            $drawdownData[] = round(-$dd, 2);
        }

        // Pair breakdown
        $pairStats = $trades->groupBy('pair')->map(function ($items, $pair) {
            $wins = $items->where('status', 'win')->count();
            $losses = $items->where('status', 'loss')->count();
            $total = $items->count();

            return [
                'pair' => $pair,
                'count' => $total,
                'wins' => $wins,
                'losses' => $losses,
                'win_rate' => ($wins + $losses) > 0 ? round(($wins / ($wins + $losses)) * 100, 1) : 0,
                'pnl' => round((float) $items->sum('pnl'), 2),
            ];
        })->sortByDesc('count')->values();

        $chartData = [
            'pnl_curve' => [
                'labels' => $pnlCurveLabels,
                'datasets' => [
                    [
                        'label' => 'Đường cong vốn ($)',
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
            'win_loss_chart' => [
                'labels' => ['Thắng', 'Thua', 'Hòa'],
                'datasets' => [
                    [
                        'data' => [$winCount, $lossCount, $breakevenCount],
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
            'total_trades' => $totalTrades,
            'win_count' => $winCount,
            'loss_count' => $lossCount,
            'breakeven_count' => $breakevenCount,
            'win_rate' => $winRate,
            'total_pnl' => $totalPnl,
            'profit_factor' => $profitFactor,
            'max_drawdown' => round($maxDrawdown, 2),
        ];

        if ($request->wantsJson()) {
            return response()->json([
                'summary' => $summary,
                'pair_stats' => $pairStats,
                'charts' => $chartData,
            ]);
        }

        return view('student.trades.stats', compact('summary', 'pairStats', 'chartData'));
    }
}
