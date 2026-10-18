@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Thống Kê Giao Dịch Toàn Học Viện</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Analytics</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Tổng hợp tỷ lệ thắng, Risk:Reward và hiệu suất trading của toàn bộ học viên</p>
        </div>
    </div>

    <!-- Overall Metrics Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <x-stat-card 
            title="Tổng số lệnh toàn hệ thống" 
            :value="$totalTrades ?? '1,428'" 
            change="+215 lệnh" 
            changeType="positive"
            subtitle="trong 30 ngày qua" 
            color="sky">
            <x-slot:icon>
                <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Tỷ lệ thắng trung bình (Win Rate)" 
            :value="($avgWinRate ?? 68.4) . '%'" 
            change="+2.8%" 
            changeType="positive"
            subtitle="mức tăng trưởng ổn định" 
            color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Tỷ lệ Risk : Reward Trung Bình" 
            :value="'1 : ' . ($avgRR ?? '2.15')" 
            change="Vượt mục tiêu 1:2" 
            changeType="positive"
            subtitle="kỷ luật quản trị rủi ro" 
            color="amber">
            <x-slot:icon>
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Overall Win Rate Pie -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-emerald-400">🥧</span>
                    Tỷ lệ Lệnh Thắng / Thua / Hòa vốn
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="overall-winrate-pie"></canvas>
            </div>
        </div>

        <!-- Trades By Pair Bar Chart -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-sky-400">📊</span>
                    Số lượng lệnh theo cặp tiền tệ
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="trades-by-pair-bar"></canvas>
            </div>
        </div>
    </div>

    <!-- Student Comparison Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>👥 Bảng So Sánh Hiệu Suất Học Viên</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3.5 px-4">Học viên</th>
                        <th class="py-3.5 px-4 text-center">Tổng lệnh</th>
                        <th class="py-3.5 px-4 text-center">Win Rate</th>
                        <th class="py-3.5 px-4 text-right">P&L Trung bình/lệnh</th>
                        <th class="py-3.5 px-4 text-center">Cặp tiền hiệu quả nhất</th>
                        <th class="py-3.5 px-4 text-right">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($studentStats ?? [] as $st)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">
                                <a href="{{ Route::has('admin.trade-stats.student') ? route('admin.trade-stats.student', $st->id) : url('/admin/trade-stats/' . $st->id) }}" class="hover:text-emerald-400">
                                    {{ $st->name }}
                                </a>
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono">{{ $st->total_trades ?? 0 }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">{{ $st->win_rate ?? 0 }}%</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-400">+${{ number_format($st->avg_pnl ?? 35, 2) }}</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-400">{{ $st->best_pair ?? 'EURUSD' }}</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('admin.trade-stats.student') ? route('admin.trade-stats.student', $st->id) : url('/admin/trade-stats/' . $st->id) }}" 
                                   class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">
                                    Xem chart &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">Vũ Đức Nam</td>
                            <td class="py-3.5 px-4 text-center font-mono">54</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">77.8%</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-400">+$42.50</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-400">EURUSD</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('admin.trade-stats.student') ? route('admin.trade-stats.student', 1) : url('/admin/trade-stats/1') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">Xem chart &rarr;</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">Lê Thuỳ Dung</td>
                            <td class="py-3.5 px-4 text-center font-mono">38</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">73.7%</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-400">+$38.20</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-400">XAUUSD</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('admin.trade-stats.student') ? route('admin.trade-stats.student', 2) : url('/admin/trade-stats/2') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">Xem chart &rarr;</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">Phạm Quang Đăng</td>
                            <td class="py-3.5 px-4 text-center font-mono">45</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">68.9%</td>
                            <td class="py-3.5 px-4 text-right font-mono font-semibold text-emerald-400">+$31.00</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-400">GBPUSD</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('admin.trade-stats.student') ? route('admin.trade-stats.student', 3) : url('/admin/trade-stats/3') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">Xem chart &rarr;</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Overall Win Rate Pie
    const pieCtx = document.getElementById('overall-winrate-pie');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Lệnh Thắng (Win)', 'Lệnh Thua (Loss)', 'Hòa vốn (Breakeven)'],
                datasets: [{
                    data: [976, 362, 90],
                    backgroundColor: ['#10b981', '#f43f5e', '#f59e0b'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: { color: '#cbd5e1', font: { size: 11 } }
                    }
                }
            }
        });
    }

    // 2. Trades by Pair Bar Chart
    const barCtx = document.getElementById('trades-by-pair-bar');
    if (barCtx) {
        new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: ['EURUSD', 'GBPUSD', 'XAUUSD', 'USDJPY', 'AUDUSD', 'USDCAD'],
                datasets: [{
                    label: 'Số lượng lệnh',
                    data: [540, 395, 280, 110, 65, 38],
                    backgroundColor: '#38bdf8',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { display: false } },
                    y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                }
            }
        });
    }
});
</script>
@endsection
