@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    @include('admin.assignments._tabs')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Quản Lý Bài Nộp Của Học Viên</h1>
            <p class="text-sm text-slate-400 mt-1">Danh sách bài nộp backtest, chart phân tích và chấm điểm cho học viên</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.assignments.create') ? route('admin.assignments.create') : url('/admin/assignments/create') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tạo đề bài tập mới
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-4 shadow-lg">
        <form method="GET" action="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Khóa học</label>
                <select name="course_id" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả khóa học --</option>
                    @if(isset($courses))
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ $c->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Trạng thái chấm</label>
                <select name="status" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ chấm (Pending)</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Đã xem (Reviewed)</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đạt (Approved)</option>
                    <option value="revision_needed" {{ request('status') == 'revision_needed' ? 'selected' : '' }}>Cần sửa lại (Revision)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Học viên</label>
                <select name="user_id" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả học viên --</option>
                    @if(isset($students))
                        @foreach($students as $st)
                            <option value="{{ $st->id }}" {{ request('user_id') == $st->id ? 'selected' : '' }}>{{ $st->name }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Bài tập</label>
                <select name="assignment_id" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả bài tập --</option>
                    @if(isset($assignments))
                        @foreach($assignments as $a)
                            <option value="{{ $a->id }}" {{ request('assignment_id') == $a->id ? 'selected' : '' }}>{{ $a->title }}</option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium transition-colors">
                    Lọc bài nộp
                </button>
                @if(request()->hasAny(['course_id', 'status', 'user_id', 'assignment_id']))
                    <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" class="py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Submissions Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3.5 px-4">Học viên</th>
                        <th class="py-3.5 px-4">Bài tập</th>
                        <th class="py-3.5 px-4">Trạng thái</th>
                        <th class="py-3.5 px-4">Thời gian nộp</th>
                        <th class="py-3.5 px-4 text-center">Điểm số</th>
                        <th class="py-3.5 px-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($submissions ?? [] as $submission)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($submission->user->name ?? 'HV', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">{{ $submission->user->name ?? 'Học viên' }}</p>
                                        <p class="text-xs text-slate-400">{{ $submission->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-200 block max-w-sm truncate">{{ $submission->assignment->title ?? 'Bài tập thực hành' }}</span>
                                <span class="text-xs text-slate-500">{{ $submission->assignment->lesson->title ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$submission->status ?? 'pending'" />
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">
                                {{ isset($submission->created_at) ? $submission->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-white">
                                {{ $submission->score !== null ? $submission->score . '/10' : '-' }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ Route::has('admin.submissions.show') ? route('admin.submissions.show', $submission->id) : url('/admin/submissions/' . $submission->id) }}" 
                                       class="px-2.5 py-1 rounded text-xs text-slate-300 hover:text-white bg-slate-700 hover:bg-slate-600 transition-colors">
                                        Xem
                                    </a>
                                    <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', $submission->id) : url('/admin/submissions/' . $submission->id . '/review') }}" 
                                       class="px-2.5 py-1 rounded text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 hover:bg-emerald-500/20 transition-colors">
                                        Chấm điểm
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <!-- Default Sample Rows -->
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">N</div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">Vũ Đức Nam</p>
                                        <p class="text-xs text-slate-400">nam.vu@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-200 block">Backtest 20 lệnh Pin Bar EURUSD</span>
                                <span class="text-xs text-slate-500">Bài 2: Vùng Supply & Demand</span>
                            </td>
                            <td class="py-3.5 px-4"><x-status-badge status="pending" label="Chờ chấm" /></td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">18/03/2026 14:20</td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-400">-</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', 1) : url('/admin/submissions/1/review') }}" class="px-2.5 py-1 rounded text-xs font-semibold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30">Chấm điểm</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-purple-400 font-bold flex items-center justify-center text-xs">D</div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">Lê Thuỳ Dung</p>
                                        <p class="text-xs text-slate-400">dung.le@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="font-medium text-slate-200 block">Kế hoạch quản lý vốn tỷ lệ 1%</span>
                                <span class="text-xs text-slate-500">Bài 1: Nguyên tắc bảo vệ vốn</span>
                            </td>
                            <td class="py-3.5 px-4"><x-status-badge status="approved" label="Đạt (9.5)" /></td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">17/03/2026 19:45</td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">9.5/10</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('admin.submissions.show') ? route('admin.submissions.show', 2) : url('/admin/submissions/2') }}" class="px-2.5 py-1 rounded text-xs text-slate-300 bg-slate-700">Xem lại</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($submissions) && method_exists($submissions, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
