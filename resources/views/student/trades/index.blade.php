@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>💹 Nhật Ký Vào Lệnh Thực Tế (Live / Demo)</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Ghi chép và theo dõi toàn bộ lệnh giao dịch thực chiến để rèn luyện kỷ luật thép</p>
        </div>

        <a href="{{ Route::has('student.trades.create') ? route('student.trades.create') : url('/student/trades/create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Ghi lệnh vào mới
        </a>
    </div>

    <!-- Stats Summary Row -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tổng lệnh vào</span>
            <p class="mt-1 text-2xl font-bold font-mono text-white">{{ $stats['total'] ?? 42 }}</p>
            <p class="text-[11px] text-slate-500 mt-0.5">Live & Demo</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tỷ lệ thắng (Winrate)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">{{ $stats['win_rate'] ?? 71.4 }}%</p>
            <p class="text-[11px] text-emerald-400 mt-0.5">30 Win / 12 Loss</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tổng Lợi Nhuận ($)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">+${{ number_format($stats['total_pnl'] ?? 1580, 2) }}</p>
            <p class="text-[11px] text-emerald-400 mt-0.5">+485 pips ròng</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Risk : Reward TB</span>
            <p class="mt-1 text-2xl font-bold font-mono text-sky-400">1:{{ $stats['avg_rr'] ?? '2.3' }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Đạt chuẩn quản trị</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 col-span-2 sm:col-span-1">
            <span class="text-xs text-slate-400 uppercase font-medium">Tài khoản Live / Demo</span>
            <p class="mt-1 text-2xl font-bold font-mono text-white">28 / 14</p>
            <p class="text-[11px] text-slate-500 mt-0.5">66.7% Real Account</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-4 shadow-lg">
        <form method="GET" action="{{ Route::has('student.trades.index') ? route('student.trades.index') : url('/student/trades') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Loại tài khoản</label>
                <select name="type" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả (Live & Demo) --</option>
                    <option value="live" {{ request('type') == 'live' ? 'selected' : '' }}>Tài khoản Real (Live)</option>
                    <option value="demo" {{ request('type') == 'demo' ? 'selected' : '' }}>Tài khoản Demo</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Cặp tiền</label>
                <select name="pair" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white font-mono focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả cặp tiền --</option>
                    @foreach(['EURUSD', 'GBPUSD', 'XAUUSD', 'USDJPY', 'AUDUSD', 'USDCAD', 'USDCHF', 'NZDUSD'] as $p)
                        <option value="{{ $p }}" {{ request('pair') == $p ? 'selected' : '' }}>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Kết quả</label>
                <select name="result" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả kết quả --</option>
                    <option value="win" {{ request('result') == 'win' ? 'selected' : '' }}>Thắng (WIN)</option>
                    <option value="loss" {{ request('result') == 'loss' ? 'selected' : '' }}>Thua (LOSS)</option>
                    <option value="breakeven" {{ request('result') == 'breakeven' ? 'selected' : '' }}>Hòa vốn (BE)</option>
                    <option value="pending" {{ request('result') == 'pending' ? 'selected' : '' }}>Đang mở (OPEN)</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium transition-colors">
                    Lọc nhật ký
                </button>
                @if(request()->hasAny(['type', 'pair', 'result']))
                    <a href="{{ Route::has('student.trades.index') ? route('student.trades.index') : url('/student/trades') }}" class="py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Trades Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700 font-mono">
                        <th class="py-3.5 px-4 font-sans">Thời gian</th>
                        <th class="py-3.5 px-4 font-sans">Loại TK</th>
                        <th class="py-3.5 px-4 font-sans">Cặp / Khung</th>
                        <th class="py-3.5 px-4 font-sans">Vị thế</th>
                        <th class="py-3.5 px-4 font-sans">Entry / SL / TP</th>
                        <th class="py-3.5 px-4 text-center font-sans">Kết quả</th>
                        <th class="py-3.5 px-4 text-right font-sans">P&L ($)</th>
                        <th class="py-3.5 px-4 text-right font-sans">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 font-mono text-xs">
                    @forelse($trades ?? [] as $t)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3 px-4 text-slate-400">
                                {{ isset($t->entry_time) ? \Carbon\Carbon::parse($t->entry_time)->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-sans">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold {{ strtolower($t->type ?? 'live') === 'live' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700 text-slate-300' }}">
                                    {{ strtoupper($t->type ?? 'LIVE') }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-white block">{{ $t->currency_pair ?? 'EURUSD' }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t->timeframe ?? 'H1' }} • Lot: {{ $t->lot_size ?? '0.10' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold {{ strtolower($t->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ strtoupper($t->direction ?? 'BUY') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                {{ $t->entry_price ?? '1.0845' }} / {{ $t->stop_loss ?? '1.0820' }} / {{ $t->take_profit ?? '1.0920' }}
                            </td>
                            <td class="py-3 px-4 text-center font-sans">
                                <x-trade-result-badge :result="$t->result ?? 'win'" />
                            </td>
                            <td class="py-3 px-4 text-right font-bold {{ ($t->pnl_amount ?? 150) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ ($t->pnl_amount ?? 150) >= 0 ? '+' : '' }}${{ number_format($t->pnl_amount ?? 150, 2) }}
                            </td>
                            <td class="py-3 px-4 text-right font-sans">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ Route::has('student.trades.show') ? route('student.trades.show', $t->id) : url('/student/trades/' . $t->id) }}" 
                                       class="p-1 rounded hover:bg-slate-700 text-slate-300 hover:text-white" title="Chi tiết">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ Route::has('student.trades.edit') ? route('student.trades.edit', $t->id) : url('/student/trades/' . $t->id . '/edit') }}" 
                                       class="p-1 rounded hover:bg-slate-700 text-slate-300 hover:text-amber-400" title="Sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/30">
                            <td class="py-3 px-4 text-slate-400">18/03/2026 15:30</td>
                            <td class="py-3 px-4 font-sans">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">LIVE</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-white">EURUSD <span class="text-[10px] text-slate-400 block font-normal">H1 • 0.20 Lot</span></td>
                            <td class="py-3 px-4 font-bold text-emerald-400">BUY</td>
                            <td class="py-3 px-4 text-slate-300">1.08450 / 1.08200 / 1.09200</td>
                            <td class="py-3 px-4 text-center font-sans"><x-trade-result-badge result="win" pips="75" /></td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-400">+$225.00</td>
                            <td class="py-3 px-4 text-right font-sans">
                                <a href="{{ Route::has('student.trades.show') ? route('student.trades.show', 1) : url('/student/trades/1') }}" class="text-xs font-semibold text-sky-400 hover:text-sky-300">Chi tiết</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30">
                            <td class="py-3 px-4 text-slate-400">17/03/2026 10:15</td>
                            <td class="py-3 px-4 font-sans">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-slate-700 text-slate-300">DEMO</span>
                            </td>
                            <td class="py-3 px-4 font-bold text-white">GBPUSD <span class="text-[10px] text-slate-400 block font-normal">H4 • 0.15 Lot</span></td>
                            <td class="py-3 px-4 font-bold text-rose-400">SELL</td>
                            <td class="py-3 px-4 text-slate-300">1.27500 / 1.27800 / 1.26600</td>
                            <td class="py-3 px-4 text-center font-sans"><x-trade-result-badge result="win" pips="90" /></td>
                            <td class="py-3 px-4 text-right font-bold text-emerald-400">+$270.00</td>
                            <td class="py-3 px-4 text-right font-sans">
                                <a href="{{ Route::has('student.trades.show') ? route('student.trades.show', 2) : url('/student/trades/2') }}" class="text-xs font-semibold text-sky-400 hover:text-sky-300">Chi tiết</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($trades) && method_exists($trades, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $trades->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
