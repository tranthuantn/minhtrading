@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" class="hover:text-white">Học viên</a>
                <span>/</span>
                <a href="{{ Route::has('admin.students.show') ? route('admin.students.show', $student->id ?? 1) : url('/admin/students/' . ($student->id ?? 1)) }}" class="hover:text-white">{{ $student->name ?? 'Học viên' }}</a>
                <span>/</span>
                <span class="text-emerald-400">Thống kê Trading chi tiết</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Phân Tích Hiệu Suất Trading:</span>
                <span class="text-emerald-400">{{ $student->name ?? 'Vũ Đức Nam' }}</span>
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.students.show') ? route('admin.students.show', $student->id ?? 1) : url('/admin/students/' . ($student->id ?? 1)) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại hồ sơ
            </a>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tỷ lệ Thắng (Winrate)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">{{ $stats['win_rate'] ?? 72.5 }}%</p>
            <p class="text-[11px] text-slate-400 mt-1">29 thắng / 11 thua</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Risk:Reward Trung bình</span>
            <p class="mt-1 text-2xl font-bold font-mono text-sky-400">1:{{ $stats['avg_rr'] ?? '2.3' }}</p>
            <p class="text-[11px] text-slate-400 mt-1">Chuẩn tỷ lệ khuyến nghị</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Profit Factor</span>
            <p class="mt-1 text-2xl font-bold font-mono text-indigo-400">{{ $stats['profit_factor'] ?? '2.64' }}</p>
            <p class="text-[11px] text-emerald-400 mt-1 font-semibold">Cực kỳ ổn định</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Max Drawdown</span>
            <p class="mt-1 text-2xl font-bold font-mono text-rose-400">-{{ $stats['max_drawdown'] ?? '4.8' }}%</p>
            <p class="text-[11px] text-slate-400 mt-1">Quản lý rủi ro tốt</p>
        </div>
    </div>

    <!-- Charts Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- P&L Equity Curve -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-emerald-400">📈</span>
                    Đường cong tăng trưởng P&L ($)
                </h3>
                <span class="text-xs text-slate-400">Cumulative Profit</span>
            </div>
            <div class="h-64 relative">
                <canvas id="pnl-chart"></canvas>
            </div>
        </div>

        <!-- Win Rate Trend -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-sky-400">📊</span>
                    Xu hướng Win Rate theo tuần (%)
                </h3>
                <span class="text-xs text-slate-400">Moving Average</span>
            </div>
            <div class="h-64 relative">
                <canvas id="win-rate-chart"></canvas>
            </div>
        </div>

        <!-- Trades By Currency Pair -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-amber-400">🎯</span>
                    Phân bổ cặp tiền giao dịch
                </h3>
                <span class="text-xs text-slate-400">Volume & Trades</span>
            </div>
            <div class="h-64 relative">
                <canvas id="pair-chart"></canvas>
            </div>
        </div>

        <!-- Monthly Performance Chart -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-purple-400">📅</span>
                    Lợi nhuận theo tháng ($)
                </h3>
                <span class="text-xs text-slate-400">Year 2026</span>
            </div>
            <div class="h-64 relative">
                <canvas id="monthly-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Monthly Performance Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white">Bảng Thống Kê Hiệu Suất Theo Tháng</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3 px-4">Tháng</th>
                        <th class="py-3 px-4 text-center">Số lệnh</th>
                        <th class="py-3 px-4 text-center">Lệnh thắng</th>
                        <th class="py-3 px-4 text-center">Lệnh thua</th>
                        <th class="py-3 px-4 text-center">Win Rate</th>
                        <th class="py-3 px-4 text-right">Lợi nhuận ($)</th>
                        <th class="py-3 px-4 text-right">Pips</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 font-mono text-xs">
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3 px-4 font-bold text-white">Tháng 03/2026</td>
                        <td class="py-3 px-4 text-center text-slate-300">14</td>
                        <td class="py-3 px-4 text-center text-emerald-400">11</td>
                        <td class="py-3 px-4 text-center text-rose-400">3</td>
                        <td class="py-3 px-4 text-center text-emerald-400 font-bold">78.6%</td>
                        <td class="py-3 px-4 text-right text-emerald-400 font-bold">+$620.00</td>
                        <td class="py-3 px-4 text-right text-emerald-400">+195 p</td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3 px-4 font-bold text-white">Tháng 02/2026</td>
                        <td class="py-3 px-4 text-center text-slate-300">16</td>
                        <td class="py-3 px-4 text-center text-emerald-400">11</td>
                        <td class="py-3 px-4 text-center text-rose-400">5</td>
                        <td class="py-3 px-4 text-center text-emerald-400 font-bold">68.8%</td>
                        <td class="py-3 px-4 text-right text-emerald-400 font-bold">+$510.00</td>
                        <td class="py-3 px-4 text-right text-emerald-400">+160 p</td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3 px-4 font-bold text-white">Tháng 01/2026</td>
                        <td class="py-3 px-4 text-center text-slate-300">10</td>
                        <td class="py-3 px-4 text-center text-emerald-400">7</td>
                        <td class="py-3 px-4 text-center text-rose-400">3</td>
                        <td class="py-3 px-4 text-center text-emerald-400 font-bold">70.0%</td>
                        <td class="py-3 px-4 text-right text-emerald-400 font-bold">+$450.00</td>
                        <td class="py-3 px-4 text-right text-emerald-400">+130 p</td>
                    </tr>
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
    // 1. PNL Curve Chart
    const pnlCtx = document.getElementById('pnl-chart');
    if (pnlCtx) {
        new Chart(pnlCtx, {
            type: 'line',
            data: {
                labels: ['Lệnh 1', 'Lệnh 5', 'Lệnh 10', 'Lệnh 15', 'Lệnh 20', 'Lệnh 25', 'Lệnh 30', 'Lệnh 35', 'Lệnh 40'],
                datasets: [{
                    label: 'Tài khoản ($)',
                    data: [1000, 1120, 1080, 1260, 1210, 1380, 1490, 1430, 1580],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#10b981',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    }

    // 2. Win Rate Chart
    const wrCtx = document.getElementById('win-rate-chart');
    if (wrCtx) {
        new Chart(wrCtx, {
            type: 'line',
            data: {
                labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4', 'Tuần 5', 'Tuần 6', 'Tuần 7', 'Tuần 8'],
                datasets: [{
                    label: 'Win Rate (%)',
                    data: [60, 65, 62, 70, 68, 75, 71, 72.5],
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3,
                    pointBackgroundColor: '#38bdf8',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } },
                    y: { min: 40, max: 100, grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    }

    // 3. Pair Chart
    const pairCtx = document.getElementById('pair-chart');
    if (pairCtx) {
        new Chart(pairCtx, {
            type: 'doughnut',
            data: {
                labels: ['EURUSD', 'GBPUSD', 'XAUUSD (Vàng)', 'USDJPY', 'AUDUSD'],
                datasets: [{
                    data: [15, 12, 8, 4, 3],
                    backgroundColor: ['#10b981', '#38bdf8', '#f59e0b', '#818cf8', '#ec4899'],
                    borderWidth: 0,
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

    // 4. Monthly Performance Chart
    const monthlyCtx = document.getElementById('monthly-chart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3'],
                datasets: [{
                    label: 'P&L ($)',
                    data: [450, 510, 620],
                    backgroundColor: '#10b981',
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } },
                    y: { grid: { color: 'rgba(255, 255, 255, 0.05)' }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    }
});
</script>
@endsection
