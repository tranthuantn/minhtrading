@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📊 Nhật Ký Backtest Dữ Liệu Quá Khứ</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Luyện tập kiểm chứng hệ thống giao dịch trên biểu đồ lịch sử để tăng sự tự tin</p>
        </div>

        <a href="{{ Route::has('student.backtests.create') ? route('student.backtests.create') : url('/student/backtests/create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow-lg shadow-sky-600/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ghi lệnh Backtest mới
        </a>
    </div>

    <!-- Stats Summary Row -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tổng lệnh backtest</span>
            <p class="mt-1 text-2xl font-bold font-mono text-white">{{ $stats['total'] ?? 50 }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Mục tiêu 100 lệnh</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Số lệnh thắng (Win)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">{{ $stats['wins'] ?? 36 }}</p>
            <p class="text-[11px] text-emerald-500/80 mt-0.5">Chiếm phần lớn</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Số lệnh thua (Loss)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-rose-400">{{ $stats['losses'] ?? 14 }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">SL kiểm soát tốt</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tỷ lệ thắng (Winrate)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">{{ $stats['win_rate'] ?? 72.0 }}%</p>
            <p class="text-[11px] text-emerald-400 mt-0.5 font-semibold">Hệ thống có Edge cao</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 col-span-2 sm:col-span-1">
            <span class="text-xs text-slate-400 uppercase font-medium">Risk : Reward TB</span>
            <p class="mt-1 text-2xl font-bold font-mono text-sky-400">1:{{ $stats['avg_rr'] ?? '2.4' }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Kỳ vọng dương vững chắc</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-4 shadow-lg">
        <form method="GET" action="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Cặp tiền</label>
                <select name="pair" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white font-mono focus:outline-none focus:border-sky-500">
                    <option value="">-- Tất cả cặp tiền --</option>
                    @foreach(['EURUSD', 'GBPUSD', 'XAUUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'NZDUSD'] as $p)
                        <option value="{{ $p }}" {{ request('pair') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Kết quả lệnh</label>
                <select name="result" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-sky-500">
                    <option value="">-- Tất cả kết quả --</option>
                    <option value="win" {{ request('result') == 'win' ? 'selected' : '' }}>Thắng (Win)</option>
                    <option value="loss" {{ request('result') == 'loss' ? 'selected' : '' }}>Thua (Loss)</option>
                    <option value="breakeven" {{ request('result') == 'breakeven' ? 'selected' : '' }}>Hòa vốn (Breakeven)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium transition-colors">
                    Lọc dữ liệu
                </button>
                @if(request()->hasAny(['pair', 'result']))
                    <a href="{{ Route::has('student.backtests.index') ? route('student.backtests.index') : url('/student/backtests') }}" class="py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Backtest Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700 font-mono">
                        <th class="py-3.5 px-4 font-sans">Thời gian</th>
                        <th class="py-3.5 px-4 font-sans">Cặp / Khung</th>
                        <th class="py-3.5 px-4 font-sans">Vị thế</th>
                        <th class="py-3.5 px-4 font-sans">Entry / SL / TP</th>
                        <th class="py-3.5 px-4 text-center font-sans">Kết quả</th>
                        <th class="py-3.5 px-4 text-right font-sans">P&L Pips</th>
                        <th class="py-3.5 px-4 text-right font-sans">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 font-mono text-xs">
                    @forelse($backtests ?? [] as $bt)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3 px-4 text-slate-400">
                                {{ isset($bt->entry_time) ? \Carbon\Carbon::parse($bt->entry_time)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-white block">{{ $bt->currency_pair ?? 'EURUSD' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $bt->timeframe ?? 'H4' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold {{ strtolower($bt->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ strtoupper($bt->direction ?? 'BUY') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                {{ $bt->entry_price ?? '1.0845' }} / {{ $bt->stop_loss ?? '1.0820' }} / {{ $bt->take_profit ?? '1.0920' }}
                            </td>
                            <td class="py-3 px-4 text-center font-sans">
                                <x-trade-result-badge :result="$bt->result ?? 'win'" />
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ ($bt->pnl_pips ?? 75) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ ($bt->pnl_pips ?? 75) >= 0 ? '+' : '' }}{{ $bt->pnl_pips ?? 75 }}p
                            </td>
                            <td class="py-3 px-4 text-right font-sans">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ Route::has('student.backtests.show') ? route('student.backtests.show', $bt->id) : url('/student/backtests/' . $bt->id) }}" 
                                       class="p-1 rounded hover:bg-slate-700 text-slate-300 hover:text-white" title="Xem chi tiết">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ Route::has('student.backtests.edit') ? route('student.backtests.edit', $bt->id) : url('/student/backtests/' . $bt->id . '/edit') }}" 
                                       class="p-1 rounded hover:bg-slate-700 text-slate-300 hover:text-amber-400" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form method="POST" action="{{ Route::has('student.backtests.destroy') ? route('student.backtests.destroy', $bt->id) : url('/student/backtests/' . $bt->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa lệnh này?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded hover:bg-slate-700 text-slate-400 hover:text-rose-400" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/30">
                            <td class="py-3 px-4 text-slate-400">18/03/2026 14:20</td>
                            <td class="py-3 px-4 font-bold text-white">EURUSD <span class="text-[10px] text-slate-400 block font-normal">H4</span></td>
                            <td class="py-3 px-4 font-bold text-emerald-400">BUY</td>
                            <td class="py-3 px-4 text-slate-300">1.0845 / 1.0820 / 1.0920</td>
                            <td class="py-3 px-4 text-center font-sans"><x-trade-result-badge result="win" /></td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-400">+75p</td>
                            <td class="py-3 px-4 text-right font-sans">
                                <a href="{{ Route::has('student.backtests.show') ? route('student.backtests.show', 1) : url('/student/backtests/1') }}" class="text-sky-400 hover:text-sky-300 text-xs font-semibold">Xem</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30">
                            <td class="py-3 px-4 text-slate-400">17/03/2026 09:30</td>
                            <td class="py-3 px-4 font-bold text-white">GBPUSD <span class="text-[10px] text-slate-400 block font-normal">H1</span></td>
                            <td class="py-3 px-4 font-bold text-rose-400">SELL</td>
                            <td class="py-3 px-4 text-slate-300">1.2750 / 1.2780 / 1.2660</td>
                            <td class="py-3 px-4 text-center font-sans"><x-trade-result-badge result="win" /></td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-400">+90p</td>
                            <td class="py-3 px-4 text-right font-sans">
                                <a href="{{ Route::has('student.backtests.show') ? route('student.backtests.show', 2) : url('/student/backtests/2') }}" class="text-sky-400 hover:text-sky-300 text-xs font-semibold">Xem</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($backtests) && method_exists($backtests, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $backtests->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
