@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" class="hover:text-white">Học viên</a>
                <span>/</span>
                <span class="text-emerald-400">Hồ sơ chi tiết</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                {{ $student->name ?? 'Học viên MinhTrading' }}
                <x-status-badge :status="($student->is_active ?? true) ? 'active' : 'inactive'" :label="($student->is_active ?? true) ? 'Đang học' : 'Tạm khóa'" />
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.students.stats') ? route('admin.students.stats', $student->id ?? 1) : url('/admin/students/' . ($student->id ?? 1) . '/stats') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white shadow-lg shadow-indigo-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Biểu đồ thống kê
            </a>
            <a href="{{ Route::has('admin.students.edit') ? route('admin.students.edit', $student->id ?? 1) : url('/admin/students/' . ($student->id ?? 1) . '/edit') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa
            </a>
        </div>
    </div>

    <!-- Student Profile Card & Stats Row -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Info Card (1 col) -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex flex-col items-center text-center">
                <div class="w-20 h-20 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-2xl border-2 border-emerald-500/50 shadow-lg shadow-emerald-500/10">
                    {{ strtoupper(substr($student->name ?? 'HV', 0, 1)) }}
                </div>
                <h3 class="mt-3 text-lg font-bold text-white">{{ $student->name ?? 'Học viên' }}</h3>
                <p class="text-xs text-slate-400">{{ $student->email ?? 'student@example.com' }}</p>
                <div class="mt-2 inline-flex items-center gap-1 text-xs text-slate-300">
                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    <span>{{ $student->phone ?? 'Chưa có SĐT' }}</span>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-slate-700/60 text-xs space-y-2.5">
                <div class="flex justify-between">
                    <span class="text-slate-400">Ngày tham gia:</span>
                    <span class="text-white font-mono">{{ isset($student->created_at) ? $student->created_at->format('d/m/Y') : '15/01/2026' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Khóa học đăng ký:</span>
                    <span class="text-white font-medium">{{ $student->courses_count ?? 2 }} khóa</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tiến độ bài học:</span>
                    <span class="text-emerald-400 font-mono font-semibold">85%</span>
                </div>
            </div>

            @if(!empty($student->bio))
                <div class="mt-4 pt-4 border-t border-slate-700/60">
                    <p class="text-xs font-semibold uppercase text-slate-400 mb-1">Ghi chú / Bio</p>
                    <p class="text-xs text-slate-300 italic">{{ $student->bio }}</p>
                </div>
            @endif
        </div>

        <!-- 4 Stat Cards in 3 Cols -->
        <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 self-start">
            <x-stat-card 
                title="Tổng số lệnh" 
                :value="$totalTrades ?? 42" 
                subtitle="Backtest & Live" 
                color="sky">
                <x-slot:icon>
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card 
                title="Tỷ lệ Thắng" 
                :value="($winRate ?? 71.4) . '%'" 
                change="30 Win / 12 Loss" 
                changeType="positive"
                color="emerald">
                <x-slot:icon>
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card 
                title="Tổng Lợi Nhuận P&L" 
                :value="'+$' . number_format($totalPnl ?? 1580, 2)" 
                change="+485 pips" 
                changeType="positive"
                color="emerald">
                <x-slot:icon>
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </x-slot:icon>
            </x-stat-card>

            <x-stat-card 
                title="Điểm bài tập TB" 
                :value="($avgScore ?? 8.6) . '/10'" 
                subtitle="Đã hoàn thành 8 bài" 
                color="purple">
                <x-slot:icon>
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                </x-slot:icon>
            </x-stat-card>
        </div>
    </div>

    <!-- Recent Trades & Submissions Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Trades Table -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-emerald-400">💹</span>
                    Lệnh giao dịch gần đây
                </h3>
            </div>

            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-700/60">
                            <th class="pb-2">Cặp / Loại</th>
                            <th class="pb-2">Vị thế</th>
                            <th class="pb-2">Kết quả</th>
                            <th class="pb-2 text-right">P&L</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse($recentTrades ?? [] as $trade)
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-bold text-white">{{ $trade->currency_pair ?? 'EURUSD' }}</span>
                                    <span class="block text-[10px] text-slate-400">{{ $trade->timeframe ?? 'H1' }} • {{ $trade->type ?? 'Backtest' }}</span>
                                </td>
                                <td class="py-2.5">
                                    <span class="font-semibold {{ ($trade->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                                        {{ strtoupper($trade->direction ?? 'BUY') }}
                                    </span>
                                </td>
                                <td class="py-2.5">
                                    <x-trade-result-badge :result="$trade->result ?? 'win'" />
                                </td>
                                <td class="py-2.5 text-right font-mono font-semibold {{ ($trade->pnl_amount ?? 120) >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ ($trade->pnl_amount ?? 120) >= 0 ? '+' : '' }}{{ number_format($trade->pnl_amount ?? 120, 2) }}$
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-bold text-white font-mono">XAUUSD</span>
                                    <span class="block text-[10px] text-slate-400">M15 • Live</span>
                                </td>
                                <td class="py-2.5">
                                    <span class="font-semibold text-emerald-400">BUY</span>
                                </td>
                                <td class="py-2.5">
                                    <x-trade-result-badge result="win" />
                                </td>
                                <td class="py-2.5 text-right font-mono font-semibold text-emerald-400">
                                    +$185.00
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-bold text-white font-mono">EURUSD</span>
                                    <span class="block text-[10px] text-slate-400">H1 • Backtest</span>
                                </td>
                                <td class="py-2.5">
                                    <span class="font-semibold text-rose-400">SELL</span>
                                </td>
                                <td class="py-2.5">
                                    <x-trade-result-badge result="loss" />
                                </td>
                                <td class="py-2.5 text-right font-mono font-semibold text-rose-400">
                                    -$75.00
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-bold text-white font-mono">GBPUSD</span>
                                    <span class="block text-[10px] text-slate-400">H4 • Live</span>
                                </td>
                                <td class="py-2.5">
                                    <span class="font-semibold text-emerald-400">BUY</span>
                                </td>
                                <td class="py-2.5">
                                    <x-trade-result-badge result="win" />
                                </td>
                                <td class="py-2.5 text-right font-mono font-semibold text-emerald-400">
                                    +$260.00
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Submissions Table -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-sky-400">📝</span>
                    Bài nộp bài tập gần đây
                </h3>
            </div>

            <div class="mt-3 overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-700/60">
                            <th class="pb-2">Bài tập</th>
                            <th class="pb-2">Trạng thái</th>
                            <th class="pb-2 text-center">Điểm</th>
                            <th class="pb-2 text-right">Xem</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse($recentSubmissions ?? [] as $sub)
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-medium text-white truncate block max-w-[200px]">{{ $sub->assignment->title ?? 'Bài tập' }}</span>
                                    <span class="text-[10px] text-slate-400 font-mono">{{ isset($sub->created_at) ? $sub->created_at->format('d/m/Y') : '14/01/2026' }}</span>
                                </td>
                                <td class="py-2.5">
                                    <x-status-badge :status="$sub->status ?? 'approved'" />
                                </td>
                                <td class="py-2.5 text-center font-mono font-bold text-white">
                                    {{ $sub->score !== null ? $sub->score . '/10' : '-' }}
                                </td>
                                <td class="py-2.5 text-right">
                                    <a href="{{ Route::has('admin.submissions.show') ? route('admin.submissions.show', $sub->id ?? 1) : url('/admin/submissions/' . ($sub->id ?? 1)) }}" 
                                       class="text-emerald-400 hover:text-emerald-300 font-semibold">
                                        Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-medium text-white truncate block max-w-[200px]">Phân tích Breakout và Retest H1</span>
                                    <span class="text-[10px] text-slate-400 font-mono">14/01/2026</span>
                                </td>
                                <td class="py-2.5">
                                    <x-status-badge status="approved" label="Đạt (9.0)" />
                                </td>
                                <td class="py-2.5 text-center font-mono font-bold text-emerald-400">9.0/10</td>
                                <td class="py-2.5 text-right">
                                    <a href="#" class="text-emerald-400 hover:text-emerald-300 font-semibold">Chi tiết</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-700/20">
                                <td class="py-2.5">
                                    <span class="font-medium text-white truncate block max-w-[200px]">Kế hoạch quản lý vốn RR 1:2</span>
                                    <span class="text-[10px] text-slate-400 font-mono">10/01/2026</span>
                                </td>
                                <td class="py-2.5">
                                    <x-status-badge status="reviewed" label="Đã chấm" />
                                </td>
                                <td class="py-2.5 text-center font-mono font-bold text-sky-400">8.5/10</td>
                                <td class="py-2.5 text-right">
                                    <a href="#" class="text-emerald-400 hover:text-emerald-300 font-semibold">Chi tiết</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
