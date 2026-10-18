@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" class="hover:text-white">Backtest</a>
                <span>/</span>
                <span class="text-sky-400">Chi tiết lệnh</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span class="font-mono">{{ $backtest->currency_pair ?? 'EURUSD' }}</span>
                <span class="text-sm font-mono {{ strtolower($backtest->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ strtoupper($backtest->direction ?? 'BUY') }} ({{ $backtest->timeframe ?? 'H4' }})
                </span>
                <x-trade-result-badge :result="$backtest->result ?? 'win'" />
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('student.backtests.edit') ? route('student.backtests.edit', $backtest->id ?? 1) : url('/student/backtests/' . ($backtest->id ?? 1) . '/edit') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa
            </a>
            <form method="POST" action="{{ Route::has('student.backtests.destroy') ? route('student.backtests.destroy', $backtest->id ?? 1) : url('/student/backtests/' . ($backtest->id ?? 1)) }}" onsubmit="return confirm('Bạn có chắc muốn xóa lệnh này?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Xóa
                </button>
            </form>
            <a href="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Trade Parameters Grid Layout -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Entry Price</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $backtest->entry_price ?? '1.08450' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Stop Loss</span>
            <span class="text-base font-bold font-mono text-rose-400 mt-1 block">{{ $backtest->stop_loss ?? '1.08200' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Take Profit</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">{{ $backtest->take_profit ?? '1.09200' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Khối lượng (Lot)</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $backtest->lot_size ?? '0.10' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">P&L Pips</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">+{{ $backtest->pnl_pips ?? 75 }} pips</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Lợi nhuận ($)</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">+${{ number_format($backtest->pnl_amount ?? 150, 2) }}</span>
        </div>
    </div>

    <!-- Chart Screenshot Gallery -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
            <span class="text-sky-400">🖼️</span>
            Ảnh chụp biểu đồ phân tích (Chart Screenshot)
        </h3>
        <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-950 flex items-center justify-center p-2 min-h-64">
            @if(!empty($backtest->chart_image))
                <img src="{{ asset('storage/' . $backtest->chart_image) }}" alt="Chart backtest" class="max-h-[500px] w-full object-contain">
            @else
                <div class="py-16 text-center text-slate-400">
                    <svg class="w-16 h-16 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm font-semibold text-slate-300">Ảnh chụp biểu đồ phân tích lệnh backtest</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Analysis Notes & Setup Criteria -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-amber-400">📝</span>
                Ghi chú phân tích & Tâm lý giao dịch
            </h3>
            <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 leading-relaxed whitespace-pre-line font-sans">
                {{ $backtest->analysis_notes ?? "1. Cấu trúc HTF: Khung H4 đang tạo Higher Highs.\n2. Vùng cản: Giá chạm vùng Demand được tạo ra từ đợt bùng nổ trước đó.\n3. Nến tín hiệu: Xuất hiện cụm nến Pin Bar từ chối giá quyết liệt.\n4. Quản trị: Giữ kỷ luật không dời SL ngược chiều, để giá tự chạy tới TP." }}
            </div>
        </div>

        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-emerald-400">✅</span>
                Tiêu chí setup đã kiểm tra
            </h3>
            <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line font-mono">
                {{ $backtest->setup_criteria ?? "- Xu hướng chính: Thuận xu hướng\n- Vị trí: Vùng cản mạnh (Demand)\n- Risk Reward: Đạt 1:3.0\n- Rủi ro tài khoản: 1.0%" }}
            </div>
        </div>
    </div>
</div>
@endsection
