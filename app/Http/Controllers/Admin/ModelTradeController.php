<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModelTrade;
use App\Models\TradeEntry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ModelTradeController extends Controller
{
    /**
     * Display a listing of model trades.
     */
    public function index(Request $request): View
    {
        $query = ModelTrade::with(['creator', 'sourceTrade.user']);

        if ($request->filled('pair')) {
            $query->where('pair', 'like', '%' . $request->input('pair') . '%');
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'published') {
                $query->where('is_published', true);
            } elseif ($status === 'draft') {
                $query->where('is_published', false);
            }
        }

        if ($request->filled('direction')) {
            $query->where('direction', $request->input('direction'));
        }

        $modelTrades = $query->latest()->paginate(15)->withQueryString();

        return view('admin.model-trades.index', compact('modelTrades'));
    }

    /**
     * Show the form for creating a new model trade from scratch.
     */
    public function create(): View
    {
        return view('admin.model-trades.create');
    }

    /**
     * Store a newly created model trade in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'pnl' => ['nullable', 'numeric'],
            'rr_ratio' => ['nullable', 'numeric'],
            'setup_name' => ['nullable', 'string', 'max:255'],
            'explanation' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'chart_image' => ['nullable', 'image', 'max:4096'],
            'chart_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $imagePath = null;
        if ($request->hasFile('chart_image')) {
            $imagePath = $request->file('chart_image')->store('model-trades', 'public');
        } elseif (! empty($validated['chart_url'])) {
            $imagePath = $validated['chart_url'];
        }

        $modelTrade = ModelTrade::create([
            'user_id' => $request->user()->id,
            'source_trade_entry_id' => null,
            'title' => $validated['title'],
            'pair' => strtoupper($validated['pair']),
            'timeframe' => $validated['timeframe'],
            'direction' => $validated['direction'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'pnl' => $validated['pnl'] ?? null,
            'rr_ratio' => $validated['rr_ratio'] ?? null,
            'chart_image' => $imagePath,
            'setup_name' => $validated['setup_name'] ?? null,
            'explanation' => $validated['explanation'],
            'notes' => $validated['notes'] ?? null,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.model-trades.show', $modelTrade->id)
            ->with('success', 'Đã tạo lệnh mẫu "' . $modelTrade->title . '" thành công.');
    }

    /**
     * Show form to create a model trade from an existing trade entry.
     */
    public function createFromTrade(TradeEntry|int $tradeEntry): View
    {
        $tradeEntry = $tradeEntry instanceof TradeEntry
            ? $tradeEntry
            : TradeEntry::with('user')->findOrFail($tradeEntry);

        return view('admin.model-trades.from-trade', compact('tradeEntry'));
    }

    /**
     * Store a model trade based on an existing student/backtest trade.
     */
    public function storeFromTrade(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'source_trade_entry_id' => ['required', 'exists:trade_entries,id'],
            'title' => ['required', 'string', 'max:255'],
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'pnl' => ['nullable', 'numeric'],
            'rr_ratio' => ['nullable', 'numeric'],
            'setup_name' => ['nullable', 'string', 'max:255'],
            'explanation' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'chart_image' => ['nullable', 'image', 'max:4096'],
            'existing_chart_url' => ['nullable', 'string', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $imagePath = $validated['existing_chart_url'] ?? null;
        if ($request->hasFile('chart_image')) {
            $imagePath = $request->file('chart_image')->store('model-trades', 'public');
        }

        $modelTrade = ModelTrade::create([
            'user_id' => $request->user()->id,
            'source_trade_entry_id' => $validated['source_trade_entry_id'],
            'title' => $validated['title'],
            'pair' => strtoupper($validated['pair']),
            'timeframe' => $validated['timeframe'],
            'direction' => $validated['direction'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'pnl' => $validated['pnl'] ?? null,
            'rr_ratio' => $validated['rr_ratio'] ?? null,
            'chart_image' => $imagePath,
            'setup_name' => $validated['setup_name'] ?? null,
            'explanation' => $validated['explanation'],
            'notes' => $validated['notes'] ?? null,
            'is_published' => $request->boolean('is_published', true),
        ]);

        return redirect()->route('admin.model-trades.show', $modelTrade->id)
            ->with('success', 'Đã chuyển đổi lệnh thực tế thành lệnh mẫu chuẩn thành công.');
    }

    /**
     * Display the specified model trade detail.
     */
    public function show(int $id): View
    {
        $modelTrade = ModelTrade::with(['creator', 'sourceTrade.user'])->findOrFail($id);

        return view('admin.model-trades.show', compact('modelTrade'));
    }

    /**
     * Show the form for editing the specified model trade.
     */
    public function edit(int $id): View
    {
        $modelTrade = ModelTrade::findOrFail($id);

        return view('admin.model-trades.edit', compact('modelTrade'));
    }

    /**
     * Update the specified model trade in storage.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $modelTrade = ModelTrade::findOrFail($id);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'pair' => ['required', 'string', 'max:50'],
            'timeframe' => ['required', 'string', 'max:20'],
            'direction' => ['required', 'in:BUY,SELL'],
            'entry_price' => ['required', 'numeric'],
            'stop_loss' => ['required', 'numeric'],
            'take_profit' => ['required', 'numeric'],
            'exit_price' => ['nullable', 'numeric'],
            'pnl' => ['nullable', 'numeric'],
            'rr_ratio' => ['nullable', 'numeric'],
            'setup_name' => ['nullable', 'string', 'max:255'],
            'explanation' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
            'chart_image' => ['nullable', 'image', 'max:4096'],
            'chart_url' => ['nullable', 'url', 'max:500'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('chart_image')) {
            if ($modelTrade->chart_image && Storage::disk('public')->exists($modelTrade->chart_image)) {
                Storage::disk('public')->delete($modelTrade->chart_image);
            }
            $modelTrade->chart_image = $request->file('chart_image')->store('model-trades', 'public');
        } elseif (! empty($validated['chart_url'])) {
            $modelTrade->chart_image = $validated['chart_url'];
        }

        $modelTrade->update([
            'title' => $validated['title'],
            'pair' => strtoupper($validated['pair']),
            'timeframe' => $validated['timeframe'],
            'direction' => $validated['direction'],
            'entry_price' => $validated['entry_price'],
            'stop_loss' => $validated['stop_loss'],
            'take_profit' => $validated['take_profit'],
            'exit_price' => $validated['exit_price'] ?? null,
            'pnl' => $validated['pnl'] ?? null,
            'rr_ratio' => $validated['rr_ratio'] ?? null,
            'setup_name' => $validated['setup_name'] ?? null,
            'explanation' => $validated['explanation'],
            'notes' => $validated['notes'] ?? null,
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()->route('admin.model-trades.show', $modelTrade->id)
            ->with('success', 'Đã cập nhật lệnh mẫu thành công.');
    }

    /**
     * Remove the specified model trade from storage.
     */
    public function destroy(int $id): RedirectResponse
    {
        $modelTrade = ModelTrade::findOrFail($id);
        $title = $modelTrade->title;

        if ($modelTrade->chart_image && Storage::disk('public')->exists($modelTrade->chart_image)) {
            Storage::disk('public')->delete($modelTrade->chart_image);
        }

        $modelTrade->delete();

        return redirect()->route('admin.model-trades.index')
            ->with('success', 'Đã xóa lệnh mẫu "' . $title . '" thành công.');
    }

    /**
     * Toggle model trade publication status.
     */
    public function toggle(int $id): RedirectResponse
    {
        $modelTrade = ModelTrade::findOrFail($id);
        $modelTrade->is_published = ! $modelTrade->is_published;
        $modelTrade->save();

        $statusText = $modelTrade->is_published ? 'kích hoạt chia sẻ' : 'chuyển sang riêng tư';

        return back()->with('success', 'Đã ' . $statusText . ' lệnh mẫu "' . $modelTrade->title . '" thành công.');
    }
}
