@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header with Quick Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Bảng Điều Khiển Quản Trị</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Hôm nay: {{ date('d/m/Y') }}</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Tổng quan hoạt động lớp học Forex, bài nộp học viên và thống kê giao dịch</p>
        </div>

        <div class="flex items-center gap-2.5 flex-wrap">
            <a href="{{ Route::has('admin.students.create') ? route('admin.students.create') : url('/admin/students/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                Thêm học viên
            </a>
            <a href="{{ Route::has('admin.assignments.create') ? route('admin.assignments.create') : url('/admin/assignments/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all">
                <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tạo bài tập mới
            </a>
            <a href="{{ Route::has('admin.model-trades.create') ? route('admin.model-trades.create') : url('/admin/model-trades/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all">
                <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                Thêm mô hình mẫu
            </a>
            <a href="{{ Route::has('admin.notifications.create') ? route('admin.notifications.create') : url('/admin/notifications/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 transition-all">
                <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                Gửi thông báo
            </a>
        </div>
    </div>

    <!-- Tuition Alert Banner if overdue or due soon -->
    @if(($overdueTuitionCount ?? 0) > 0 || ($dueSoonTuitionCount ?? 0) > 0)
        <div class="p-4 rounded-xl bg-gradient-to-r from-slate-850 via-slate-800 to-slate-850 border border-slate-700 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div>
                    <h4 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Cảnh báo học phí cần lưu ý:</span>
                        @if(($overdueTuitionCount ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                🔴 {{ $overdueTuitionCount }} học viên chưa nộp tiền
                            </span>
                        @endif
                        @if(($dueSoonTuitionCount ?? 0) > 0)
                            <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                🟡 {{ $dueSoonTuitionCount }} học viên sắp hết hạn (≤ 5 ngày)
                            </span>
                        @endif
                    </h4>
                    <p class="text-xs text-slate-400 mt-0.5">Kiểm tra kỳ hạn, gửi thông báo nhắc học phí 1-Click hoặc ghi nhận thu tiền cho học viên.</p>
                </div>
            </div>
            <a href="{{ route('admin.tuition.index') }}" 
               class="px-4 py-2 rounded-xl text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-white transition flex items-center gap-1.5 whitespace-nowrap self-start sm:self-auto">
                <span>Quản lý học phí ngay</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
        </div>
    @endif

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-stat-card 
            title="Tổng học viên" 
            :value="$totalStudents ?? 48" 
            change="+4 học viên" 
            changeType="positive"
            subtitle="so với tháng trước" 
            color="sky">
            <x-slot:icon>
                <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Tổng khóa học" 
            :value="$totalCourses ?? 6" 
            subtitle="Đang xuất bản" 
            color="indigo">
            <x-slot:icon>
                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Bài nộp chờ chấm" 
            :value="$pendingSubmissionsCount ?? 12" 
            change="Cần xử lý sớm" 
            changeType="negative"
            subtitle="ưu tiên cao" 
            color="amber">
            <x-slot:icon>
                <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            </x-slot:icon>
        </x-stat-card>

        <x-stat-card 
            title="Lệnh tháng này" 
            :value="$monthlyTradesCount ?? 342" 
            change="+18.4%" 
            changeType="positive"
            subtitle="tổng volume vào lệnh" 
            color="emerald">
            <x-slot:icon>
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
            </x-slot:icon>
        </x-stat-card>
    </div>

    <!-- Main 2-Column Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Pending Submissions (2 cols) -->
        <div class="lg:col-span-2 rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                <div class="flex items-center gap-2">
                    <div class="w-2.5 h-2.5 rounded-full bg-amber-400 animate-pulse"></div>
                    <h2 class="text-base font-bold text-white">Bài nộp đang chờ chấm điểm</h2>
                </div>
                <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index', ['status' => 'pending']) : url('/admin/submissions?status=pending') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center gap-1">
                    Xem tất cả
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>

            <div class="mt-4 overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/60">
                            <th class="pb-3 pl-2">Học viên</th>
                            <th class="pb-3">Bài tập</th>
                            <th class="pb-3">Thời gian nộp</th>
                            <th class="pb-3">Trạng thái</th>
                            <th class="pb-3 pr-2 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse($pendingSubmissions ?? [] as $sub)
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 pl-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">
                                            {{ strtoupper(substr($sub->user->name ?? 'HV', 0, 1)) }}
                                        </div>
                                        <span class="font-medium text-white">{{ $sub->user->name ?? 'Học viên' }}</span>
                                    </div>
                                </td>
                                <td class="py-3 text-slate-300">
                                    <span class="truncate block max-w-xs">{{ $sub->assignment->title ?? 'Bài tập thực hành' }}</span>
                                </td>
                                <td class="py-3 text-xs text-slate-400 font-mono">
                                    {{ isset($sub->created_at) ? $sub->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-3">
                                    <x-status-badge :status="$sub->status ?? 'pending'" />
                                </td>
                                <td class="py-3 pr-2 text-right">
                                    <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', $sub->id ?? 1) : url('/admin/submissions/' . ($sub->id ?? 1) . '/review') }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20">
                                        Chấm bài
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <!-- Fallback sample rows if empty -->
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 pl-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">M</div>
                                        <div>
                                            <p class="font-medium text-white leading-tight">Nguyễn Văn Minh</p>
                                            <p class="text-[11px] text-slate-400">minh.nv@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-slate-300">
                                    <span class="truncate block max-w-xs">Backtest 10 lệnh mô hình Pin Bar EURUSD</span>
                                </td>
                                <td class="py-3 text-xs text-slate-400 font-mono">10 phút trước</td>
                                <td class="py-3">
                                    <x-status-badge status="pending" label="Chờ chấm" />
                                </td>
                                <td class="py-3 pr-2 text-right">
                                    <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', 1) : url('/admin/submissions/1/review') }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20">
                                        Chấm bài
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 pl-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-slate-700 text-purple-400 font-bold flex items-center justify-center text-xs">H</div>
                                        <div>
                                            <p class="font-medium text-white leading-tight">Trần Hoàng Hà</p>
                                            <p class="text-[11px] text-slate-400">ha.tran@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-slate-300">
                                    <span class="truncate block max-w-xs">Xác định cấu trúc Market Structure H4 XAUUSD</span>
                                </td>
                                <td class="py-3 text-xs text-slate-400 font-mono">42 phút trước</td>
                                <td class="py-3">
                                    <x-status-badge status="pending" label="Chờ chấm" />
                                </td>
                                <td class="py-3 pr-2 text-right">
                                    <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', 2) : url('/admin/submissions/2/review') }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20">
                                        Chấm bài
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-700/30 transition-colors">
                                <td class="py-3 pl-2">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-7 h-7 rounded-full bg-slate-700 text-amber-400 font-bold flex items-center justify-center text-xs">T</div>
                                        <div>
                                            <p class="font-medium text-white leading-tight">Lê Tuấn Tú</p>
                                            <p class="text-[11px] text-slate-400">tu.tuan@example.com</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 text-slate-300">
                                    <span class="truncate block max-w-xs">Nhật ký 5 lệnh Breakout GBPUSD tuần 42</span>
                                </td>
                                <td class="py-3 text-xs text-slate-400 font-mono">2 giờ trước</td>
                                <td class="py-3">
                                    <x-status-badge status="pending" label="Chờ chấm" />
                                </td>
                                <td class="py-3 pr-2 text-right">
                                    <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', 3) : url('/admin/submissions/3/review') }}" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-md text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20">
                                        Chấm bài
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Top Students by Win Rate (1 col) -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
                <div class="flex items-center gap-2">
                    <span class="text-amber-400">🏆</span>
                    <h2 class="text-base font-bold text-white">Top Học Viên (Win Rate)</h2>
                </div>
                <a href="{{ Route::has('admin.trade-stats.index') ? route('admin.trade-stats.index') : url('/admin/trade-stats') }}" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300">
                    Bảng xếp hạng
                </a>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($topStudents ?? [] as $index => $topStudent)
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/60 border border-slate-800 hover:border-slate-700 transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black {{ $index === 0 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : ($index === 1 ? 'bg-slate-300 text-slate-950' : ($index === 2 ? 'bg-amber-700 text-white' : 'text-slate-400 bg-slate-800')) }}">
                                {{ $index + 1 }}
                            </span>
                            <div>
                                <p class="text-sm font-semibold text-white">{{ $topStudent->name ?? 'Học viên' }}</p>
                                <p class="text-[11px] text-slate-400 font-mono">{{ $topStudent->total_trades ?? 20 }} lệnh đã trade</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold font-mono text-emerald-400">{{ $topStudent->win_rate ?? 75 }}%</span>
                            <p class="text-[10px] text-slate-400">Tỷ lệ thắng</p>
                        </div>
                    </div>
                @empty
                    <!-- Sample Top 5 -->
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/60 border border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20">1</span>
                            <div>
                                <p class="text-sm font-semibold text-white">Vũ Đức Nam</p>
                                <p class="text-[11px] text-slate-400 font-mono">54 lệnh • RR 1:2.4</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold font-mono text-emerald-400">77.8%</span>
                            <p class="text-[10px] text-emerald-500/80 font-mono">+1,240$</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/60 border border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black bg-slate-300 text-slate-950">2</span>
                            <div>
                                <p class="text-sm font-semibold text-white">Lê Thuỳ Dung</p>
                                <p class="text-[11px] text-slate-400 font-mono">38 lệnh • RR 1:2.1</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold font-mono text-emerald-400">73.7%</span>
                            <p class="text-[10px] text-emerald-500/80 font-mono">+980$</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/60 border border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black bg-amber-700 text-white">3</span>
                            <div>
                                <p class="text-sm font-semibold text-white">Phạm Quang Đăng</p>
                                <p class="text-[11px] text-slate-400 font-mono">45 lệnh • RR 1:1.8</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold font-mono text-emerald-400">68.9%</span>
                            <p class="text-[10px] text-emerald-500/80 font-mono">+640$</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/60 border border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black bg-slate-800 text-slate-400">4</span>
                            <div>
                                <p class="text-sm font-semibold text-white">Nguyễn Khánh Linh</p>
                                <p class="text-[11px] text-slate-400 font-mono">29 lệnh • RR 1:2.0</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold font-mono text-emerald-400">65.5%</span>
                            <p class="text-[10px] text-emerald-500/80 font-mono">+490$</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900/60 border border-slate-800">
                        <div class="flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-black bg-slate-800 text-slate-400">5</span>
                            <div>
                                <p class="text-sm font-semibold text-white">Đỗ Minh Quân</p>
                                <p class="text-[11px] text-slate-400 font-mono">33 lệnh • RR 1:1.7</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-bold font-mono text-emerald-400">63.6%</span>
                            <p class="text-[10px] text-emerald-500/80 font-mono">+415$</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
