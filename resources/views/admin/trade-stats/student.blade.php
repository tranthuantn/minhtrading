@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Student Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-slate-800 border border-slate-700 text-sky-400 font-bold flex items-center justify-center text-xl">
                {{ strtoupper(substr($student->name ?? 'HV', 0, 1)) }}
            </div>
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                    <a href="{{ Route::has('admin.trade-stats.index') ? route('admin.trade-stats.index') : url('/admin/trade-stats') }}" class="hover:text-white">Thống kê Trading</a>
                    <span>/</span>
                    <span class="text-emerald-400">Chi tiết học viên</span>
                </div>
                <h1 class="text-2xl font-bold tracking-tight text-white">{{ $student->name ?? 'Vũ Đức Nam' }}</h1>
                <p class="text-xs text-slate-400">{{ $student->email ?? 'nam.vu@example.com' }} • Đang tham gia khóa Price Action Thực Chiến</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.students.show') ? route('admin.students.show', $student->id ?? 1) : url('/admin/students/' . ($student->id ?? 1)) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                Xem hồ sơ
            </a>
            <a href="{{ Route::has('admin.trade-stats.index') ? route('admin.trade-stats.index') : url('/admin/trade-stats') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Quick Metrics Row -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tổng lệnh đã vào</span>
            <p class="mt-1 text-2xl font-bold font-mono text-white">{{ $student->trades_count ?? 54 }}</p>
            <p class="text-[11px] text-slate-400 mt-1">42 thắng • 12 thua</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tỷ lệ Thắng (Winrate)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">{{ $student->win_rate ?? 77.8 }}%</p>
            <p class="text-[11px] text-emerald-400 mt-1">+4.2% so với tuần trước</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tổng Lợi Nhuận ($)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">+$1,450.00</p>
            <p class="text-[11px] text-emerald-400 mt-1">+380 pips ròng</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Risk : Reward TB</span>
            <p class="mt-1 text-2xl font-bold font-mono text-sky-400">1:2.4</p>
            <p class="text-[11px] text-slate-400 mt-1">Đạt chuẩn quản trị</p>
        </div>
    </div>

    <!-- 4 Canvas Elements for Chart.js -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- PNL Chart -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-emerald-400">📈</span>
                    Đường cong tăng trưởng P&L ($)
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="pnl-chart"></canvas>
            </div>
        </div>

        <!-- Win Rate Chart -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-sky-400">📊</span>
                    Biểu đồ tỷ lệ thắng theo tuần (%)
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="win-rate-chart"></canvas>
            </div>
        </div>

        <!-- Pair Chart -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-amber-400">🎯</span>
                    Phân bổ cặp tiền giao dịch
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="pair-chart"></canvas>
            </div>
        </div>

        <!-- Monthly Chart -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-purple-400">📅</span>
                    Lợi nhuận theo tháng ($)
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="monthly-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Detailed Trade History Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>📑 Lịch Sử Vào Lệnh Chi Tiết Của Học Viên</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3 px-4">Thời gian</th>
                        <th class="py-3 px-4">Cặp tiền</th>
                        <th class="py-3 px-4">Vị thế</th>
                        <th class="py-3 px-4">Entry / SL / TP</th>
                        <th class="py-3 px-4 text-center">Kết quả</th>
                        <th class="py-3 px-4 text-right">Pips</th>
                        <th class="py-3 px-4 text-right">P&L ($)</th>
                        <th class="py-3 px-4 text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-xs font-mono">
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3 px-4 text-slate-400">18/03/2026 15:30</td>
                        <td class="py-3 px-4 font-bold text-white">EURUSD</td>
                        <td class="py-3 px-4 font-bold text-emerald-400">BUY</td>
                        <td class="py-3 px-4 text-slate-300">1.0845 / 1.0820 / 1.0920</td>
                        <td class="py-3 px-4 text-center"><x-trade-result-badge result="win" /></td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-400">+75p</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-400">+$225.00</td>
                        <td class="py-3 px-4 text-center font-sans">
                            <a href="{{ Route::has('admin.model-trades.from-trade') ? route('admin.model-trades.from-trade', ['trade_id' => 1]) : url('/admin/model-trades/from-trade?trade_id=1') }}" 
                               class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                                🏆 Thành mẫu
                            </a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3 px-4 text-slate-400">17/03/2026 10:15</td>
                        <td class="py-3 px-4 font-bold text-white">GBPUSD</td>
                        <td class="py-3 px-4 font-bold text-rose-400">SELL</td>
                        <td class="py-3 px-4 text-slate-300">1.2750 / 1.2780 / 1.2660</td>
                        <td class="py-3 px-4 text-center"><x-trade-result-badge result="win" /></td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-400">+90p</td>
                        <td class="py-3 px-4 text-right font-bold text-emerald-400">+$270.00</td>
                        <td class="py-3 px-4 text-center font-sans">
                            <a href="{{ Route::has('admin.model-trades.from-trade') ? route('admin.model-trades.from-trade', ['trade_id' => 2]) : url('/admin/model-trades/from-trade?trade_id=2') }}" 
                               class="text-xs font-semibold text-amber-400 hover:text-amber-300">
                                🏆 Thành mẫu
                            </a>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3 px-4 text-slate-400">15/03/2026 18:45</td>
                        <td class="py-3 px-4 font-bold text-white">XAUUSD</td>
                        <td class="py-3 px-4 font-bold text-emerald-400">BUY</td>
                        <td class="py-3 px-4 text-slate-300">2155.0 / 2150.0 / 2170.0</td>
                        <td class="py-3 px-4 text-center"><x-trade-result-badge result="loss" /></td>
                        <td class="py-3 px-4 text-right font-bold text-rose-400">-50p</td>
                        <td class="py-3 px-4 text-right font-bold text-rose-400">-$100.00</td>
                        <td class="py-3 px-4 text-center font-sans text-slate-500">-</td>
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
    // 1. PNL Chart
    const pnlEl = document.getElementById('pnl-chart');
    if (pnlEl) {
        new Chart(pnlEl, {
            type: 'line',
            data: {
                labels: ['L1', 'L5', 'L10', 'L15', 'L20', 'L25', 'L30', 'L35', 'L40', 'L45', 'L50', 'L54'],
                datasets: [{
                    label: 'P&L Tích lũy ($)',
                    data: [1000, 1150, 1080, 1290, 1240, 1450, 1620, 1550, 1780, 1950, 2150, 2450],
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                    y: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                }
            }
        });
    }

    // 2. Win Rate Chart
    const wrEl = document.getElementById('win-rate-chart');
    if (wrEl) {
        new Chart(wrEl, {
            type: 'line',
            data: {
                labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8'],
                datasets: [{
                    label: 'Win Rate (%)',
                    data: [65, 70, 68, 75, 72, 80, 76, 77.8],
                    borderColor: '#38bdf8',
                    backgroundColor: 'rgba(56, 189, 248, 0.1)',
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } },
                    y: { min: 50, max: 100, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                }
            }
        });
    }

    // 3. Pair Chart
    const pairEl = document.getElementById('pair-chart');
    if (pairEl) {
        new Chart(pairEl, {
            type: 'doughnut',
            data: {
                labels: ['EURUSD', 'GBPUSD', 'XAUUSD', 'USDJPY'],
                datasets: [{
                    data: [24, 18, 8, 4],
                    backgroundColor: ['#10b981', '#38bdf8', '#f59e0b', '#818cf8'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { color: '#cbd5e1', font: { size: 11 } } }
                }
            }
        });
    }

    // 4. Monthly Chart
    const monthlyEl = document.getElementById('monthly-chart');
    if (monthlyEl) {
        new Chart(monthlyEl, {
            type: 'bar',
            data: {
                labels: ['Tháng 1', 'Tháng 2', 'Tháng 3'],
                datasets: [{
                    label: 'Lợi nhuận ($)',
                    data: [380, 490, 580],
                    backgroundColor: '#10b981',
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
