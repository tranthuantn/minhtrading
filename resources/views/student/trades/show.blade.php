@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('student.trades.index') ? route('student.trades.index') : url('/student/trades') }}" class="hover:text-white">Vào lệnh</a>
                <span>/</span>
                <span class="text-emerald-400">Chi tiết lệnh</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span class="font-mono">{{ $trade->currency_pair ?? 'EURUSD' }}</span>
                <span class="text-sm font-mono {{ strtolower($trade->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ strtoupper($trade->direction ?? 'BUY') }} ({{ $trade->timeframe ?? 'H1' }})
                </span>
                <span class="px-2 py-0.5 rounded text-xs font-bold font-mono {{ strtolower($trade->type ?? 'live') === 'live' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700 text-slate-300' }}">
                    {{ strtoupper($trade->type ?? 'LIVE') }}
                </span>
                <x-trade-result-badge :result="$trade->result ?? 'win'" />
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('student.trades.edit') ? route('student.trades.edit', $trade->id ?? 1) : url('/student/trades/' . ($trade->id ?? 1) . '/edit') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white shadow transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa
            </a>
            <form method="POST" action="{{ Route::has('student.trades.destroy') ? route('student.trades.destroy', $trade->id ?? 1) : url('/student/trades/' . ($trade->id ?? 1)) }}" onsubmit="return confirm('Bạn có chắc muốn xóa lệnh này?')" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    Xóa
                </button>
            </form>
            <a href="{{ Route::has('student.trades.index') ? route('student.trades.index') : url('/student/trades') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Trade Parameters Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Entry Price</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $trade->entry_price ?? '1.08450' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Stop Loss</span>
            <span class="text-base font-bold font-mono text-rose-400 mt-1 block">{{ $trade->stop_loss ?? '1.08200' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Take Profit</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">{{ $trade->take_profit ?? '1.09200' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Khối lượng</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $trade->lot_size ?? '0.10' }} Lot</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Lợi nhuận ($)</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">+${{ number_format($trade->pnl_amount ?? 225, 2) }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Pips đạt được</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">+{{ $trade->pnl_pips ?? 75 }} pips</span>
        </div>
    </div>

    <!-- Chart Screenshot -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
            <span class="text-sky-400">🖼️</span>
            Ảnh chụp biểu đồ phân tích (Chart Screenshot)
        </h3>
        <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-950 flex items-center justify-center p-2 min-h-64">
            @if(!empty($trade->chart_image))
                <img src="{{ asset('storage/' . $trade->chart_image) }}" alt="Chart trade" class="max-h-[500px] w-full object-contain">
            @else
                <div class="py-16 text-center text-slate-400">
                    <svg class="w-16 h-16 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm font-semibold text-slate-300">Ảnh chụp biểu đồ phân tích lệnh vào thực tế</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Analysis Notes & Setup Criteria -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-amber-400">📝</span>
                Ghi chú tâm lý & Kỷ luật lúc trade
            </h3>
            <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-200 leading-relaxed whitespace-pre-line font-sans">
                {{ $trade->analysis_notes ?? "Tâm lý lúc vào lệnh rất bình tĩnh vì đã tính toán trước rủi ro tối đa 1% tài khoản. Khi giá đi được 1R, em dời SL về BE để bảo toàn vốn và giữ nguyên kế hoạch đến TP." }}
            </div>
        </div>

        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-emerald-400">✅</span>
                Điều kiện kiểm tra trước khi bấm lệnh
            </h3>
            <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line font-mono">
                {{ $trade->setup_criteria ?? "- Rủi ro tài khoản <= 1%: Đạt\n- RR >= 1:2: Đạt 1:3.0\n- Không trade trước tin tức FOMC: Đạt\n- Đã đặt sẵn SL/TP trên MT4/MT5: Có" }}
            </div>
        </div>
    </div>
</div>
@endsection
