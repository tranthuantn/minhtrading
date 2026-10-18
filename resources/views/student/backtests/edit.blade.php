@extends('layouts.student')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" class="hover:text-white">Backtest</a>
                <span>/</span>
                <span class="text-sky-400">Chỉnh sửa lệnh</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Chỉnh Sửa Lệnh Backtest #{{ $backtest->id ?? 1 }}</h1>
            <p class="text-sm text-slate-400">Cập nhật lại giá vào lệnh, cắt lỗ, chốt lời hoặc ảnh biểu đồ</p>
        </div>
        <a href="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('student.backtests.update') ? route('student.backtests.update', $backtest->id ?? 1) : url('/student/backtests/' . ($backtest->id ?? 1)) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Parameters Grid 1 -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- Currency Pair -->
                <div>
                    <label for="currency_pair" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Cặp tiền tệ <span class="text-rose-400">*</span>
                    </label>
                    <select name="currency_pair" id="currency_pair" required class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:border-sky-500">
                        @foreach(['EURUSD', 'GBPUSD', 'XAUUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'USDCHF', 'NZDUSD', 'GBPJPY', 'EURJPY'] as $p)
                            <option value="{{ $p }}" {{ old('currency_pair', $backtest->currency_pair ?? '') == $p ? 'selected' : '' }}>{{ $p }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Direction -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Vị thế lệnh <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg border border-slate-700 bg-slate-900 cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="direction" value="buy" {{ old('direction', $backtest->direction ?? 'buy') == 'buy' ? 'checked' : '' }} class="text-emerald-500">
                            <span class="text-xs font-bold text-emerald-400 font-mono">BUY</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg border border-slate-700 bg-slate-900 cursor-pointer hover:border-rose-500">
                            <input type="radio" name="direction" value="sell" {{ old('direction', $backtest->direction ?? '') == 'sell' ? 'checked' : '' }} class="text-rose-500">
                            <span class="text-xs font-bold text-rose-400 font-mono">SELL</span>
                        </label>
                    </div>
                </div>

                <!-- Timeframe -->
                <div>
                    <label for="timeframe" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Khung thời gian <span class="text-rose-400">*</span>
                    </label>
                    <select name="timeframe" id="timeframe" required class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:border-sky-500">
                        @foreach(['M1', 'M5', 'M15', 'M30', 'H1', 'H4', 'D1', 'W1'] as $tf)
                            <option value="{{ $tf }}" {{ old('timeframe', $backtest->timeframe ?? 'H4') == $tf ? 'selected' : '' }}>{{ $tf }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Entry Price -->
                <div>
                    <label for="entry_price" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Giá vào lệnh (Entry) <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="entry_price" id="entry_price" value="{{ old('entry_price', $backtest->entry_price ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:border-sky-500" />
                </div>

                <!-- Stop Loss -->
                <div>
                    <label for="stop_loss" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Cắt lỗ (Stop Loss) <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="stop_loss" id="stop_loss" value="{{ old('stop_loss', $backtest->stop_loss ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-rose-400 font-mono text-sm focus:border-sky-500" />
                </div>

                <!-- Take Profit -->
                <div>
                    <label for="take_profit" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Chốt lời (Take Profit) <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="take_profit" id="take_profit" value="{{ old('take_profit', $backtest->take_profit ?? '') }}" required class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-emerald-400 font-mono text-sm focus:border-sky-500" />
                </div>

                <!-- Lot Size -->
                <div>
                    <label for="lot_size" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Khối lượng (Lot Size)
                    </label>
                    <input type="number" step="0.01" name="lot_size" id="lot_size" value="{{ old('lot_size', $backtest->lot_size ?? '0.10') }}" class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:border-sky-500" />
                </div>

                <!-- Result -->
                <div>
                    <label for="result" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Kết quả lệnh <span class="text-rose-400">*</span>
                    </label>
                    <select name="result" id="result" required class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:border-sky-500">
                        <option value="win" {{ old('result', $backtest->result ?? '') == 'win' ? 'selected' : '' }}>Thắng (WIN)</option>
                        <option value="loss" {{ old('result', $backtest->result ?? '') == 'loss' ? 'selected' : '' }}>Thua (LOSS)</option>
                        <option value="breakeven" {{ old('result', $backtest->result ?? '') == 'breakeven' ? 'selected' : '' }}>Hòa vốn (BE)</option>
                        <option value="pending" {{ old('result', $backtest->result ?? '') == 'pending' ? 'selected' : '' }}>Đang mở (OPEN)</option>
                    </select>
                </div>

                <!-- PnL Pips -->
                <div>
                    <label for="pnl_pips" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Lợi nhuận (Pips)
                    </label>
                    <input type="number" step="0.1" name="pnl_pips" id="pnl_pips" value="{{ old('pnl_pips', $backtest->pnl_pips ?? '') }}" class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:border-sky-500" />
                </div>
            </div>

            <!-- Analysis Notes -->
            <div>
                <label for="analysis_notes" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Ghi chú phân tích & Lý do vào lệnh <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="analysis_notes" 
                    id="analysis_notes" 
                    rows="4" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:border-sky-500"
                >{{ old('analysis_notes', $backtest->analysis_notes ?? '') }}</textarea>
            </div>

            <!-- Setup Criteria -->
            <div>
                <label for="setup_criteria" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tiêu chí setup thỏa mãn
                </label>
                <textarea 
                    name="setup_criteria" 
                    id="setup_criteria" 
                    rows="3" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm font-mono focus:border-sky-500"
                >{{ old('setup_criteria', $backtest->setup_criteria ?? '') }}</textarea>
            </div>

            <!-- Chart Screenshot -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Ảnh chụp biểu đồ phân tích (Tải ảnh mới nếu muốn thay)
                </label>
                <input 
                    type="file" 
                    name="chart_image" 
                    accept="image/*" 
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-sky-400 hover:file:bg-slate-600 cursor-pointer"
                />
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow-lg shadow-sky-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Cập nhật lệnh Backtest
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
