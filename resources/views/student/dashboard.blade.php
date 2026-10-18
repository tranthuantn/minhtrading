@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Chào mừng trở lại,</span>
                <span class="text-sky-400">{{ auth()->user()->name ?? 'Học viên' }}</span>
                <span>👋</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Hôm nay: {{ date('l, d/m/Y') }} • Kỷ luật và tuân thủ quản lý vốn là chìa khóa thành công</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ Route::has('student.backtests.create') ? route('student.backtests.create') : url('/student/backtests/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow-lg shadow-sky-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Ghi chép Backtest
            </a>
            <a href="{{ Route::has('student.trades.create') ? route('student.trades.create') : url('/student/trades/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                Vào lệnh mới
            </a>
        </div>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-stat-card 
            title="Tổng số lệnh đã trade" 
            :value="$totalTrades ?? 42" 
            change="+6 lệnh tuần này" 
            changeType="positive"
            subtitle="Backtest & Live" 
            color="sky">
            <x-slot:icon>
                <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Tỷ lệ thắng (Win Rate)" 
            :value="($winRate ?? 71.4) . '%'" 
            change="+3.2%" 
            changeType="positive"
            subtitle="Mục tiêu duy trì > 65%" 
            color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Tổng lợi nhuận P&L" 
            :value="'+$' . number_format($totalPnl ?? 1580, 2)" 
            change="+485 pips" 
            changeType="positive"
            subtitle="Tỷ lệ RR đạt 1:2.4" 
            color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Bài tập chưa nộp" 
            :value="$pendingAssignmentsCount ?? 2" 
            change="Sắp hết hạn" 
            changeType="negative"
            subtitle="Hoàn thành để lên cấp" 
            color="amber">
            <x-slot:icon>
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- 2 Column Section: Notifications & Pending Assignments -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Unread Notifications (Latest 3) -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-sky-400">🔔</span>
                    Thông báo mới từ giảng viên
                </h3>
                <a href="{{ Route::has('student.notifications.index') ? route('student.notifications.index') : url('/student/notifications') }}" class="text-xs text-sky-400 hover:text-sky-300 font-semibold">
                    Xem tất cả &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($notifications ?? [] as $noti)
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60 hover:border-slate-600 transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">{{ $noti->title }}</span>
                            <span class="text-[10px] text-slate-400 font-mono">{{ isset($noti->created_at) ? $noti->created_at->format('d/m/Y') : '18/03/2026' }}</span>
                        </div>
                        <p class="text-xs text-slate-400 line-clamp-2">{{ $noti->message }}</p>
                    </div>
                @empty
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">Lịch Live Trading tuần này trên Zoom</span>
                            <span class="text-[10px] text-slate-400 font-mono">18/03/2026</span>
                        </div>
                        <p class="text-xs text-slate-400">Thứ 3 và Thứ 5 lúc 19:30. Anh em chuẩn bị sẵn biểu đồ EURUSD và Vàng...</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-white">Thầy Minh đã chấm bài tập Backtest tuần 42</span>
                            <span class="text-[10px] text-slate-400 font-mono">16/03/2026</span>
                        </div>
                        <p class="text-xs text-slate-400">Bạn đạt 9.0/10 điểm! Nhận xét: Bắt đúng nhịp quét thanh khoản Liquidity Sweep...</p>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60">
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs font-bold text-amber-400">Cảnh báo tin tức mạnh: Bản tin Non-farm (NFP)</span>
                            <span class="text-[10px] text-slate-400 font-mono">14/03/2026</span>
                        </div>
                        <p class="text-xs text-slate-400">Lưu ý quản trị vốn, không mở lệnh 30 phút trước và sau khi ra tin...</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Pending Assignments List -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-amber-400">📝</span>
                    Bài tập cần hoàn thành
                </h3>
                <a href="{{ Route::has('student.assignments.index') ? route('student.assignments.index') : url('/student/assignments') }}" class="text-xs text-sky-400 hover:text-sky-300 font-semibold">
                    Xem danh sách &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($pendingAssignments ?? [] as $assignment)
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">{{ $assignment->title }}</span>
                            <span class="text-[11px] text-slate-400 font-mono mt-0.5 block">Hạn nộp: {{ isset($assignment->due_date) ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y') : 'Không giới hạn' }}</span>
                        </div>
                        <a href="{{ Route::has('student.assignments.show') ? route('student.assignments.show', $assignment->id) : url('/student/assignments/' . $assignment->id) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow transition-colors">
                            Nộp bài
                        </a>
                    </div>
                @empty
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Backtest 15 lệnh mô hình Pin Bar EURUSD</span>
                            <span class="text-[11px] text-slate-400 font-mono mt-0.5 block">Hạn chót: 25/10/2026 23:59</span>
                        </div>
                        <a href="{{ Route::has('student.assignments.show') ? route('student.assignments.show', 1) : url('/student/assignments/1') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow">Nộp bài</a>
                    </div>
                    <div class="p-3 rounded-lg bg-slate-900/60 border border-slate-700/60 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold text-white block">Xác định vùng Order Block & Supply Demand H4</span>
                            <span class="text-[11px] text-slate-400 font-mono mt-0.5 block">Hạn chót: 30/10/2026 23:59</span>
                        </div>
                        <a href="{{ Route::has('student.assignments.show') ? route('student.assignments.show', 2) : url('/student/assignments/2') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow">Nộp bài</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Recent Trades (Latest 5) -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
        <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-emerald-400">💹</span>
                Lệnh giao dịch gần nhất
            </h3>
            <a href="{{ Route::has('student.trades.index') ? route('student.trades.index') : url('/student/trades') }}" class="text-xs text-sky-400 hover:text-sky-300 font-semibold">
                Xem toàn bộ nhật ký &rarr;
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-700/60 font-semibold">
                        <th class="pb-2.5">Thời gian</th>
                        <th class="pb-2.5">Cặp tiền</th>
                        <th class="pb-2.5">Vị thế</th>
                        <th class="pb-2.5">Entry / SL / TP</th>
                        <th class="pb-2.5 text-center">Kết quả</th>
                        <th class="pb-2.5 text-right">P&L ($)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/40 font-mono">
                    @forelse($recentTrades ?? [] as $t)
                        <tr class="hover:bg-slate-700/20">
                            <td class="py-2.5 text-slate-400">{{ isset($t->created_at) ? $t->created_at->format('d/m H:i') : '18/03 14:20' }}</td>
                            <td class="py-2.5 font-bold text-white">{{ $t->currency_pair ?? 'EURUSD' }}</td>
                            <td class="py-2.5 font-bold {{ strtolower($t->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">{{ strtoupper($t->direction ?? 'BUY') }}</td>
                            <td class="py-2.5 text-slate-300">{{ $t->entry_price ?? '1.0845' }} / {{ $t->stop_loss ?? '1.0820' }} / {{ $t->take_profit ?? '1.0920' }}</td>
                            <td class="py-2.5 text-center"><x-trade-result-badge :result="$t->result ?? 'win'" /></td>
                            <td class="py-2.5 text-right font-bold {{ ($t->pnl_amount ?? 120) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ ($t->pnl_amount ?? 120) >= 0 ? '+' : '' }}{{ number_format($t->pnl_amount ?? 120, 2) }}$
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/20">
                            <td class="py-2.5 text-slate-400">18/03 15:30</td>
                            <td class="py-2.5 font-bold text-white">EURUSD</td>
                            <td class="py-2.5 font-bold text-emerald-400">BUY</td>
                            <td class="py-2.5 text-slate-300">1.08450 / 1.08200 / 1.09200</td>
                            <td class="py-2.5 text-center"><x-trade-result-badge result="win" pips="75" /></td>
                            <td class="py-2.5 text-right font-bold text-emerald-400">+$225.00</td>
                        </tr>
                        <tr class="hover:bg-slate-700/20">
                            <td class="py-2.5 text-slate-400">17/03 10:15</td>
                            <td class="py-2.5 font-bold text-white">GBPUSD</td>
                            <td class="py-2.5 font-bold text-rose-400">SELL</td>
                            <td class="py-2.5 text-slate-300">1.27500 / 1.27800 / 1.26600</td>
                            <td class="py-2.5 text-center"><x-trade-result-badge result="win" pips="90" /></td>
                            <td class="py-2.5 text-right font-bold text-emerald-400">+$270.00</td>
                        </tr>
                        <tr class="hover:bg-slate-700/20">
                            <td class="py-2.5 text-slate-400">15/03 18:45</td>
                            <td class="py-2.5 font-bold text-white">XAUUSD</td>
                            <td class="py-2.5 font-bold text-emerald-400">BUY</td>
                            <td class="py-2.5 text-slate-300">2155.00 / 2150.00 / 2170.00</td>
                            <td class="py-2.5 text-center"><x-trade-result-badge result="loss" pips="-50" /></td>
                            <td class="py-2.5 text-right font-bold text-rose-400">-$100.00</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
