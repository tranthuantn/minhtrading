@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{
    duplicateOpen: false,
    assignmentId: null,
    assignmentTitle: '',
    assignmentSource: '',
    newTitle: '',
    dueDate: '{{ now()->addDays(7)->format('Y-m-d\TH:i') }}',
    openDuplicateModal(id, title, course, lesson) {
        this.assignmentId = id;
        this.assignmentTitle = title;
        this.newTitle = title;
        this.assignmentSource = course + ' • ' + lesson;
        this.duplicateOpen = true;
    }
}">
    @include('admin.assignments._tabs')

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📝 Quản Lý Bài Tập Theo Khóa Học</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Quản lý bài tập theo từng khóa học. Hỗ trợ nhân bản và tái sử dụng đề bài cho các khóa mới chỉ với 1 click.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ Route::has('admin.assignments.stats') ? route('admin.assignments.stats') : url('/admin/assignments/stats') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-sky-400 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                Thống kê hoàn thành
            </a>
            <a href="{{ Route::has('admin.assignments.create') ? route('admin.assignments.create') : url('/admin/assignments/create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tạo bài tập mới
            </a>
        </div>
    </div>

    <!-- Filters: Lọc Khóa học, Loại bài tập, Trạng thái bài nộp -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-4 shadow-lg space-y-3.5">
        <!-- Quick Course Tabs -->
        <div>
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">📚 Lọc theo khóa học:</span>
                @if(request()->hasAny(['course_id', 'type', 'status', 'search']))
                    <a href="{{ route('admin.assignments.index') }}" class="text-[11px] text-sky-400 hover:text-sky-300">
                        ✕ Xóa bộ lọc
                    </a>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.assignments.index', array_filter(['type' => request('type'), 'status' => request('status')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('course_id') ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                    Tất cả khóa học ({{ $courses->count() }})
                </a>
                @foreach($courses as $c)
                    <a href="{{ route('admin.assignments.index', array_filter(['course_id' => $c->id, 'type' => request('type'), 'status' => request('status')])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ request('course_id') == $c->id ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                        <span>{{ Str::limit($c->title, 35) }}</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('course_id') == $c->id ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-800 text-slate-400' }}">
                            {{ $c->assignments_count ?? $c->lessons_count }} bài tập
                        </span>
                    </a>
                @endforeach
            </div>
        </div>

        <form method="GET" action="{{ route('admin.assignments.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-700/60">
            @if(request('course_id'))
                <input type="hidden" name="course_id" value="{{ request('course_id') }}">
            @endif

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Loại bài tập</label>
                <select name="type" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả loại bài --</option>
                    <option value="analysis" {{ request('type') == 'analysis' ? 'selected' : '' }}>Phân tích biểu đồ (Chart Analysis)</option>
                    <option value="backtest" {{ request('type') == 'backtest' ? 'selected' : '' }}>Nhật ký Backtest</option>
                    <option value="trade_entry" {{ request('type') == 'trade_entry' ? 'selected' : '' }}>Vào lệnh thực tế (Live/Demo)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Trạng thái bài nộp</label>
                <select name="status" class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Có bài nộp chờ chấm</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Đã có bài chấm đạt</option>
                    <option value="no_submissions" {{ request('status') == 'no_submissions' ? 'selected' : '' }}>Chưa có bài nộp</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1">Tìm kiếm đề bài</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nhập tên bài tập..." 
                       class="w-full px-3 py-2 text-sm rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 placeholder-slate-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium transition-colors">
                    Lọc dữ liệu
                </button>
                @if(request()->hasAny(['type', 'status', 'search']))
                    <a href="{{ route('admin.assignments.index', array_filter(['course_id' => request('course_id')])) }}" class="py-2 px-3 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- DANH SÁCH BÀI TẬP CHIA THEO KHÓA HỌC -->
    @php
        $displayCourses = request('course_id') 
            ? $courses->where('id', request('course_id')) 
            : $courses;
    @endphp

    @forelse($displayCourses as $course)
        @php
            $courseAssignments = $assignmentsByCourse->get($course->id, collect());
        @endphp

        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden space-y-0">
            <!-- Course Header -->
            <div class="p-4 bg-slate-900/90 border-b border-slate-700/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 font-bold text-lg shrink-0">
                        📚
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-white">{{ $course->title }}</h2>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                {{ $courseAssignments->count() }} bài tập • {{ $course->lessons->count() }} bài học
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $course->description }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.assignments.create', ['course_id' => $course->id]) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600/20 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/40 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Thêm bài tập cho khóa này
                    </a>
                    <a href="{{ route('admin.courses.show', $course->id) }}" 
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                        Chi tiết khóa học
                    </a>
                </div>
            </div>

            <!-- Table of Assignments for this Course -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-900/50 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/60">
                            <th class="py-3 px-4 min-w-[260px]">Tiêu đề bài tập</th>
                            <th class="py-3 px-4 min-w-[200px]">Bài học tương ứng</th>
                            <th class="py-3 px-4">Loại bài</th>
                            <th class="py-3 px-4">Hạn nộp</th>
                            <th class="py-3 px-4 text-center">Tình trạng nộp bài</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse($courseAssignments as $assignment)
                            <tr class="hover:bg-slate-750/30 transition-colors">
                                <!-- Title -->
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('admin.assignments.show', $assignment->id) }}" 
                                       class="font-semibold text-white hover:text-emerald-400 transition-colors block text-sm">
                                        {{ $assignment->title }}
                                    </a>
                                </td>

                                <!-- Lesson -->
                                <td class="py-3.5 px-4">
                                    <span class="text-xs text-slate-300 font-medium block">
                                        {{ $assignment->lesson->title ?? 'Bài học liên quan' }}
                                    </span>
                                </td>

                                <!-- Type -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @php
                                        $typeMap = [
                                            'backtest' => ['bg' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30', 'label' => '📊 Backtest'],
                                            'trade_entry' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'label' => '💹 Vào lệnh'],
                                            'analysis' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'label' => '🔍 Phân tích Chart'],
                                        ];
                                        $tInfo = $typeMap[$assignment->type ?? 'analysis'] ?? $typeMap['analysis'];
                                    @endphp
                                    <span class="px-2 py-0.5 rounded text-xs font-medium border {{ $tInfo['bg'] }}">
                                        {{ $tInfo['label'] }}
                                    </span>
                                </td>

                                <!-- Deadline -->
                                <td class="py-3.5 px-4 text-xs font-mono text-slate-400 whitespace-nowrap">
                                    {{ isset($assignment->due_date) ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : 'Không giới hạn' }}
                                </td>

                                <!-- Submissions Count & Status -->
                                <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5">
                                        <a href="{{ route('admin.submissions.index', ['assignment_id' => $assignment->id]) }}" 
                                           class="px-2.5 py-1 rounded-full text-xs font-bold font-mono bg-slate-900 border border-slate-700 text-sky-400 hover:border-sky-500 transition-colors">
                                            {{ $assignment->submissions_count ?? 0 }} bài đã nộp
                                        </a>
                                        @if(($assignment->pending_submissions_count ?? 0) > 0)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 animate-pulse">
                                                {{ $assignment->pending_submissions_count }} chờ chấm
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <!-- 1-Click Duplicate Button -->
                                        <button type="button" 
                                                @click="openDuplicateModal({{ $assignment->id }}, '{{ addslashes($assignment->title) }}', '{{ addslashes($assignment->lesson->course->title ?? '') }}', '{{ addslashes($assignment->lesson->title ?? '') }}')"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-sky-400 hover:bg-slate-700/60" 
                                                title="Nhân bản / Tái sử dụng đề bài sang khóa khác (1-Click)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        </button>

                                        <a href="{{ route('admin.assignments.show', $assignment->id) }}" 
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-slate-700/60" title="Xem danh sách bài nộp">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>
                                        <a href="{{ route('admin.assignments.edit', $assignment->id) }}" 
                                           class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-700/60" title="Chỉnh sửa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.assignments.destroy', $assignment->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa bài tập này?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-700/60" title="Xóa">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    <p class="text-xs text-slate-400">Không có bài tập nào phù hợp với bộ lọc trong khóa học này.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-12 text-center text-slate-500">
            <svg class="w-12 h-12 mx-auto mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
            <p class="text-sm font-semibold text-slate-300">Không tìm thấy bài tập nào</p>
            <p class="text-xs text-slate-500 mt-1">Bấm "Tạo bài tập mới" để giao đề thực hành cho học viên hoặc nhân bản từ các khóa khác.</p>
        </div>
    @endforelse

    <!-- 1-Click Duplicate Modal -->
    <div x-show="duplicateOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="duplicateOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="duplicateOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="duplicateOpen" x-transition.scale class="inline-block align-bottom bg-slate-900 border border-slate-700 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-sky-500/20 text-sky-400 font-bold text-base">📋</span>
                        <div>
                            <h3 class="text-base font-bold text-white">Nhân Bản Đề Bài Tập (1-Click)</h3>
                            <p class="text-xs text-slate-400">Tái sử dụng nội dung đề bài này cho một khóa học khác</p>
                        </div>
                    </div>
                    <button type="button" @click="duplicateOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
                </div>

                <!-- Source Assignment Info -->
                <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs space-y-1">
                    <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Đề bài gốc:</span>
                    <p class="font-bold text-sky-300 text-sm" x-text="assignmentTitle"></p>
                    <p class="text-slate-400" x-text="'Nguồn: ' + assignmentSource"></p>
                </div>

                <form :action="'{{ url('/admin/assignments') }}/' + assignmentId + '/duplicate'" method="POST" class="space-y-4">
                    @csrf
                    <!-- Target Lesson Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Chọn Khóa học & Bài học đích cần giao đề này <span class="text-rose-400">*</span>
                        </label>
                        <select name="lesson_id" required class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500">
                            <option value="">-- Chọn bài học nhận đề bài này --</option>
                            @foreach($courses as $c)
                                <optgroup label="Khóa: {{ $c->title }}">
                                    @foreach($c->lessons as $l)
                                        <option value="{{ $l->id }}">{{ $l->title }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>

                    <!-- Custom Title if desired -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Tiêu đề bài tập mới (Tùy chọn sửa đổi)
                        </label>
                        <input type="text" name="title" x-model="newTitle" placeholder="Giữ nguyên hoặc nhập tiêu đề mới..." class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500">
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Hạn nộp bài mới (Deadline)
                        </label>
                        <input type="datetime-local" name="due_date" x-model="dueDate" class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" @click="duplicateOpen = false" class="px-4 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Xác nhận Nhân bản (1-Click)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
