@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    @include('admin.assignments._tabs')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" class="hover:text-white">Bài tập</a>
                <span>/</span>
                <span class="text-emerald-400">Thống kê hoàn thành</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Thống Kê Tiến Độ & Kết Quả Bài Tập</h1>
            <p class="text-sm text-slate-400">Theo dõi tỷ lệ hoàn thành, chất lượng bài nộp và điểm số trung bình của toàn bộ học viên</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Quick Stats Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Tỷ lệ hoàn thành chung</span>
            <p class="mt-1 text-2xl font-bold font-mono text-emerald-400">84.2%</p>
            <p class="text-[11px] text-slate-400 mt-1">168 / 200 bài đã nộp</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Điểm số trung bình</span>
            <p class="mt-1 text-2xl font-bold font-mono text-sky-400">8.4 / 10</p>
            <p class="text-[11px] text-emerald-400 mt-1">+0.3 so với tháng trước</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Chờ chấm điểm</span>
            <p class="mt-1 text-2xl font-bold font-mono text-amber-400">12 bài</p>
            <p class="text-[11px] text-slate-400 mt-1">Cần xem xét sớm</p>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 uppercase font-medium">Cần sửa lại (Revision)</span>
            <p class="mt-1 text-2xl font-bold font-mono text-rose-400">5 bài</p>
            <p class="text-[11px] text-slate-400 mt-1">Đã gửi feedback</p>
        </div>
    </div>

    <!-- Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Status Doughnut -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-amber-400">🍩</span>
                    Phân bổ trạng thái bài nộp
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="assignment-status-chart"></canvas>
            </div>
        </div>

        <!-- Average Scores Bar -->
        <div class="p-5 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-sky-400">📊</span>
                    Điểm số trung bình theo bài tập
                </h3>
            </div>
            <div class="h-64 relative">
                <canvas id="assignment-scores-chart"></canvas>
            </div>
        </div>
    </div>

    <!-- Assignment Completion Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white">Bảng Thống Kê Chi Tiết Từng Bài Tập</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3 px-4">Bài tập</th>
                        <th class="py-3 px-4">Khóa học</th>
                        <th class="py-3 px-4 text-center">Đã nộp / Tổng</th>
                        <th class="py-3 px-4 text-center">Tỷ lệ hoàn thành</th>
                        <th class="py-3 px-4 text-center">Điểm TB</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50 text-xs">
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3.5 px-4 font-semibold text-white">Backtest 20 lệnh Pin Bar EURUSD</td>
                        <td class="py-3.5 px-4 text-slate-400">Price Action & Cấu Trúc Thị Trường</td>
                        <td class="py-3.5 px-4 text-center font-mono">42 / 48</td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-emerald-400 h-1.5 rounded-full" style="width: 87.5%"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold">87.5%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">8.8 / 10</td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3.5 px-4 font-semibold text-white">Vẽ vùng Supply Demand trên 5 cặp Forex</td>
                        <td class="py-3.5 px-4 text-slate-400">Price Action & Cấu Trúc Thị Trường</td>
                        <td class="py-3.5 px-4 text-center font-mono">45 / 48</td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-emerald-400 h-1.5 rounded-full" style="width: 93.7%"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold">93.7%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-400">8.5 / 10</td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3.5 px-4 font-semibold text-white">Lập bảng kế hoạch quản trị rủi ro 1%</td>
                        <td class="py-3.5 px-4 text-slate-400">Quản Trị Vốn & Tâm Lý Giao Dịch</td>
                        <td class="py-3.5 px-4 text-center font-mono">38 / 48</td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-emerald-400 h-1.5 rounded-full" style="width: 79.1%"></div>
                                </div>
                                <span class="font-mono text-emerald-400 font-bold">79.1%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">8.9 / 10</td>
                    </tr>
                    <tr class="hover:bg-slate-700/30">
                        <td class="py-3.5 px-4 font-semibold text-white">Nhật ký 5 lệnh Breakout Live/Demo</td>
                        <td class="py-3.5 px-4 text-slate-400">Chiến Lược Vào Lệnh Đa Khung</td>
                        <td class="py-3.5 px-4 text-center font-mono">35 / 48</td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-20 bg-slate-700 rounded-full h-1.5">
                                    <div class="bg-amber-400 h-1.5 rounded-full" style="width: 72.9%"></div>
                                </div>
                                <span class="font-mono text-amber-400 font-bold">72.9%</span>
                            </div>
                        </td>
                        <td class="py-3.5 px-4 text-center font-mono font-bold text-sky-400">7.9 / 10</td>
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
    // 1. Status Doughnut Chart
    const statusCtx = document.getElementById('assignment-status-chart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Đạt (Approved)', 'Đã chấm (Reviewed)', 'Chờ chấm (Pending)', 'Cần sửa lại (Revision)'],
                datasets: [{
                    data: [115, 36, 12, 5],
                    backgroundColor: ['#10b981', '#38bdf8', '#f59e0b', '#f43f5e'],
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

    // 2. Average Scores Bar Chart
    const scoresCtx = document.getElementById('assignment-scores-chart');
    if (scoresCtx) {
        new Chart(scoresCtx, {
            type: 'bar',
            data: {
                labels: ['Bài 1: Pin Bar', 'Bài 2: Supply/Demand', 'Bài 3: Quản trị vốn', 'Bài 4: Breakout', 'Bài 5: SMC FVG'],
                datasets: [{
                    label: 'Điểm trung bình (/10)',
                    data: [8.8, 8.5, 8.9, 7.9, 8.4],
                    backgroundColor: '#38bdf8',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { color: '#94a3b8', font: { size: 11 } }, grid: { display: false } },
                    y: { min: 0, max: 10, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(255, 255, 255, 0.05)' } }
                }
            }
        });
    }
});
</script>
@endsection
