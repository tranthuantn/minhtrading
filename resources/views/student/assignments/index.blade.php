@extends('layouts.student')

@section('content')
<script>
    function assignmentHub() {
        return {
            openModal: {{ $errors->any() && old('content') ? 'true' : 'false' }},
            scope: '{{ old('scope', 'lesson') }}',
            selectedCourseId: '{{ old('course_id', $selectedCourseId ?? '') }}',
            selectedLessonId: '{{ old('lesson_id', '') }}',
            coursesWithLessons: @json($enrolledCoursesWithLessons ?? []),
            get availableLessons() {
                if (!this.selectedCourseId) return [];
                const course = this.coursesWithLessons.find(c => c.id == this.selectedCourseId);
                return course ? (course.lessons || []) : [];
            }
        };
    }
</script>

<div x-data="assignmentHub()" class="space-y-6">

    <!-- Flash message -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
        </div>
    @endif

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
            <p class="font-semibold mb-1">Đã có lỗi xảy ra, vui lòng kiểm tra lại:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Header & Tab Navigation -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                @if($activeTab === 'questions')
                    <span>💬 Hỏi Đáp & Thảo Luận Học Tập</span>
                @else
                    <span>📝 Bài Tập & Bài Nộp Theo Khóa Học</span>
                @endif
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                @if($activeTab === 'questions')
                    Đặt câu hỏi trong từng bài học hoặc trao đổi chung ngoài giáo trình với Giảng viên và cộng đồng học viên
                @else
                    Mỗi khóa học có số lượng bài tập tương ứng với các bài học. Quản lý tiến độ làm bài, nộp bài và nhận xét của Giảng viên
                @endif
            </p>
        </div>

        <!-- Action on Header -->
        <div class="flex items-center gap-3">
            @if($activeTab === 'questions')
                <button type="button" @click="openModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold bg-gradient-to-r from-sky-500 to-indigo-600 hover:from-sky-400 hover:to-indigo-500 text-white shadow-lg shadow-sky-500/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Đặt câu hỏi mới</span>
                </button>
            @endif
        </div>
    </div>

    <!-- TABS SWITCHER -->
    <div class="flex border-b border-slate-800 gap-2">
        <a href="{{ route('student.assignments.index', ['tab' => 'assignments']) }}" 
           class="flex items-center gap-2 py-3 px-4 border-b-2 font-medium text-sm transition-all {{ $activeTab !== 'questions' ? 'border-sky-500 text-sky-400 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
            <span>📝 Bài Tập & Bài Nộp</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $activeTab !== 'questions' ? 'bg-sky-500/20 text-sky-300' : 'bg-slate-800 text-slate-400' }}">
                {{ $stats['total'] ?? 0 }}
            </span>
        </a>
        <a href="{{ route('student.assignments.index', ['tab' => 'questions']) }}" 
           class="flex items-center gap-2 py-3 px-4 border-b-2 font-medium text-sm transition-all {{ $activeTab === 'questions' ? 'border-indigo-500 text-indigo-400 font-semibold' : 'border-transparent text-slate-400 hover:text-slate-200 hover:border-slate-700' }}">
            <span>💬 Hỏi Đáp & Thảo Luận</span>
            <span class="px-2 py-0.5 rounded-full text-xs {{ $activeTab === 'questions' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-slate-800 text-slate-400' }}">
                {{ $questionsStats['total'] ?? 0 }}
            </span>
            @if(($questionsStats['unanswered'] ?? 0) > 0)
                <span class="w-2 h-2 rounded-full bg-amber-400" title="Có câu hỏi đang chờ giải đáp"></span>
            @endif
        </a>
    </div>

    <!-- ========================================== -->
    <!-- TAB 1: BÀI TẬP & BÀI NỘP                   -->
    <!-- ========================================== -->
    @if($activeTab !== 'questions')
        <!-- Quick Stats Cards (Assignments) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Total -->
            <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'course_id' => $selectedCourseId])) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ !request('status') ? 'border-sky-500/50 bg-slate-800 ring-1 ring-sky-500/30' : 'border-slate-700/80' }} hover:border-sky-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Tổng bài tập</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-white">{{ $stats['total'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-slate-900 text-sky-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </span>
                </div>
            </a>

            <!-- Not Submitted -->
            <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'not_submitted', 'course_id' => $selectedCourseId])) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ request('status') === 'not_submitted' ? 'border-amber-500/50 bg-slate-800 ring-1 ring-amber-500/30' : 'border-slate-700/80' }} hover:border-amber-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Chưa nộp (Cần làm)</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-amber-400">{{ $stats['not_submitted'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
            </a>

            <!-- Pending Review -->
            <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'pending', 'course_id' => $selectedCourseId])) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ request('status') === 'pending' ? 'border-sky-500/50 bg-slate-800 ring-1 ring-sky-500/30' : 'border-slate-700/80' }} hover:border-sky-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Đã nộp chờ chấm</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-sky-400">{{ $stats['pending'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-sky-500/10 text-sky-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </span>
                </div>
            </a>

            <!-- Reviewed -->
            <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'reviewed', 'course_id' => $selectedCourseId])) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ request('status') === 'reviewed' ? 'border-emerald-500/50 bg-slate-800 ring-1 ring-emerald-500/30' : 'border-slate-700/80' }} hover:border-emerald-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Đã chấm điểm</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-emerald-400">{{ $stats['reviewed'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </span>
                </div>
            </a>
        </div>

        <!-- Filters: Lọc Khóa Học & Lọc Trạng Thái -->
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg space-y-3.5">
            <!-- 1. Lọc Khóa Học -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span>📚 Khóa học được cấp quyền:</span>
                    </span>
                    @if($selectedCourseId || request('status'))
                        <a href="{{ route('student.assignments.index', ['tab' => 'assignments']) }}" class="text-[11px] text-sky-400 hover:text-sky-300">
                            ✕ Xóa toàn bộ bộ lọc
                        </a>
                    @endif
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => request('status')])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !$selectedCourseId ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                        📚 Tất cả khóa học ({{ $enrolledCourses->count() }})
                    </a>
                    @foreach($enrolledCourses as $c)
                        <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'course_id' => $c->id, 'status' => request('status')])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ $selectedCourseId == $c->id ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                            <span>{{ Str::limit($c->title, 35) }}</span>
                            <span class="px-1.5 py-0.2 rounded text-[10px] {{ $selectedCourseId == $c->id ? 'bg-sky-800 text-sky-100' : 'bg-slate-800 text-slate-400' }}">
                                {{ $courseStats[$c->id]['total_assignments'] ?? $c->lessons_count }} bài
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-700/60 pt-3">
                <!-- 2. Lọc Trạng Thái -->
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                        ⚡ Trạng thái làm bài:
                    </span>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'course_id' => $selectedCourseId])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        Tất cả ({{ $stats['total'] ?? 0 }})
                    </a>

                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'not_submitted', 'course_id' => $selectedCourseId])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'not_submitted' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        Chưa nộp ({{ $stats['not_submitted'] ?? 0 }})
                    </a>

                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'pending', 'course_id' => $selectedCourseId])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'pending' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        Chờ chấm ({{ $stats['pending'] ?? 0 }})
                    </a>

                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'reviewed', 'course_id' => $selectedCourseId])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'reviewed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        Đã chấm điểm ({{ $stats['reviewed'] ?? 0 }})
                    </a>

                    @if(($stats['revision_needed'] ?? 0) > 0)
                        <a href="{{ route('student.assignments.index', array_filter(['tab' => 'assignments', 'status' => 'revision_needed', 'course_id' => $selectedCourseId])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'revision_needed' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                            Cần sửa lại ({{ $stats['revision_needed'] }})
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- DANH SÁCH BÀI TẬP CHIA THEO TỪNG KHÓA HỌC -->
        @php
            $displayCourses = $selectedCourseId 
                ? $enrolledCourses->where('id', $selectedCourseId) 
                : $enrolledCourses;
        @endphp

        @forelse($displayCourses as $course)
            @php
                $courseAssignments = $assignmentsByCourse->get($course->id, collect());
                $cStats = $courseStats[$course->id] ?? ['total_assignments' => 0, 'completed' => 0, 'percent' => 0];
            @endphp

            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden space-y-0">
                <!-- Header Khóa Học -->
                <div class="p-4 bg-slate-900/90 border-b border-slate-700/80 flex flex-col md:flex-row md:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-sky-500/10 text-sky-400 flex items-center justify-center font-bold text-sm">
                            📚
                        </span>
                        <div>
                            <div class="flex items-center gap-2">
                                <h3 class="text-base font-bold text-white">{{ $course->title }}</h3>
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $courseAssignments->count() }} bài tập
                                </span>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">{{ Str::limit($course->description, 100) }}</p>
                        </div>
                    </div>

                    <!-- Tiến độ của học viên trong khóa -->
                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400 block">Tiến độ bài tập:</span>
                            <span class="text-xs font-bold text-emerald-400">
                                {{ $cStats['completed'] }}/{{ $cStats['total_assignments'] }} hoàn thành ({{ $cStats['percent'] }}%)
                            </span>
                        </div>
                        <div class="w-24 bg-slate-800 rounded-full h-2 overflow-hidden border border-slate-700">
                            <div class="bg-gradient-to-r from-sky-500 to-emerald-500 h-2 rounded-full transition-all" style="width: {{ $cStats['percent'] }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Bảng Bài Tập -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-300">
                        <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50 font-semibold">
                            <tr>
                                <th class="py-3 px-4">Bài học & Đề bài tập</th>
                                <th class="py-3 px-4">Hình thức & Hạn nộp</th>
                                <th class="py-3 px-4 text-center">Trạng thái</th>
                                <th class="py-3 px-4 text-center">Điểm số</th>
                                <th class="py-3 px-4 text-right">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @forelse($courseAssignments as $assignment)
                                @php
                                    $sub = $assignment->submissions->first();
                                    $status = $sub ? $sub->status : 'not_submitted';
                                    $review = $sub ? $sub->reviews->first() : null;
                                @endphp
                                <tr class="hover:bg-slate-750/50 transition">
                                    <!-- Lesson & Title -->
                                    <td class="py-3.5 px-4">
                                        <div class="flex items-start gap-2.5">
                                            <span class="w-6 h-6 rounded bg-slate-900 border border-slate-700 text-slate-400 text-xs flex items-center justify-center font-bold flex-shrink-0 mt-0.5">
                                                {{ $assignment->lesson?->sort_order ?? '•' }}
                                            </span>
                                            <div>
                                                <a href="{{ route('student.assignments.show', $assignment->id) }}" class="font-medium text-white hover:text-sky-400 transition block">
                                                    {{ $assignment->title }}
                                                </a>
                                                <span class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                                    <span>Bài học:</span>
                                                    <span class="text-sky-400">{{ $assignment->lesson?->title ?? 'N/A' }}</span>
                                                </span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Type & Due Date -->
                                    <td class="py-3.5 px-4 whitespace-nowrap">
                                        <div class="space-y-1">
                                            @php
                                                $submissionModeLabels = [
                                                    'chart_images' => ['🖼️ Ảnh Chart phân tích', 'border-blue-500/30 text-blue-400 bg-blue-500/10'],
                                                    'trade_data' => ['💹 Dữ liệu vào lệnh P&L', 'border-emerald-500/30 text-emerald-400 bg-emerald-500/10'],
                                                    'both' => ['⚡ Ảnh Chart + Dữ liệu lệnh', 'border-indigo-500/30 text-indigo-400 bg-indigo-500/10'],
                                                    'text_notes' => ['📝 Ghi chú văn bản / file', 'border-amber-500/30 text-amber-400 bg-amber-500/10'],
                                                ];
                                                $mode = $assignment->submission_mode ?? 'chart_images';
                                                $mConfig = $submissionModeLabels[$mode] ?? ['🖼️ Ảnh Chart', 'border-slate-700 text-slate-400 bg-slate-800'];
                                            @endphp
                                            <span class="px-2 py-0.5 rounded text-[11px] font-medium border {{ $mConfig[1] }} inline-block">
                                                {{ $mConfig[0] }}
                                            </span>
                                            <div class="text-xs text-slate-400 flex items-center gap-1">
                                                @if($assignment->due_date)
                                                    <span class="{{ $assignment->due_date->isPast() && !$sub ? 'text-rose-400 font-medium' : 'text-slate-400' }}">
                                                        ⏱ Hạn: {{ $assignment->due_date->format('d/m/Y H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-slate-500">Không giới hạn thời gian</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if(!$sub)
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                                Chưa nộp bài
                                            </span>
                                        @elseif($status === 'pending')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-sky-400"></span>
                                                Chờ chấm điểm
                                            </span>
                                        @elseif($status === 'reviewed')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 inline-flex items-center gap-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                Đã chấm điểm
                                            </span>
                                        @elseif($status === 'revision_needed')
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-400 border border-rose-500/30 inline-flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                                Cần sửa lại
                                            </span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-700 text-slate-300">
                                                {{ ucfirst($status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Score -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        @if($review && $review->score !== null)
                                            <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold font-mono text-sm">
                                                <span>{{ number_format($review->score, 1) }}</span>
                                                <span class="text-xs text-emerald-500/70">/10</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-slate-500 font-mono">--</span>
                                        @endif
                                    </td>

                                    <!-- Action Button -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        @if(!$sub)
                                            <a href="{{ route('student.assignments.show', $assignment->id) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow transition-all">
                                                Làm bài ngay
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                            </a>
                                        @elseif($status === 'revision_needed')
                                            <a href="{{ route('student.assignments.show', $assignment->id) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow transition-all">
                                                Sửa & Nộp lại
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                            </a>
                                        @else
                                            <a href="{{ route('student.assignments.show', $assignment->id) }}" 
                                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-sky-400 hover:text-white transition-all">
                                                Xem bài & nhận xét
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-slate-500">
                                        @if(request('status') === 'not_submitted')
                                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 text-xs font-medium">
                                                <span>🎉 Tuyệt vời! Bạn đã hoàn thành toàn bộ bài tập trong khóa học này.</span>
                                            </div>
                                        @else
                                            <p class="text-xs text-slate-400">Không có bài tập nào thuộc trạng thái này trong khóa học.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-12 text-center text-slate-500">
                <svg class="w-12 h-12 mx-auto mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <p class="text-sm font-semibold text-slate-300">Bạn chưa được cấp quyền tham gia khóa học nào</p>
                <p class="text-xs text-slate-500 mt-1">Vui lòng liên hệ Giảng viên để được kích hoạt quyền truy cập vào các bài học và bài tập.</p>
            </div>
        @endforelse
    @endif

    <!-- ========================================== -->
    <!-- TAB 2: HỎI ĐÁP & THẢO LUẬN                 -->
    <!-- ========================================== -->
    @if($activeTab === 'questions')
        <!-- Quick Stats Cards (Questions) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <!-- Total Questions -->
            <a href="{{ route('student.assignments.index', ['tab' => 'questions']) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ !request('q_status') && !request('only_mine') && !request('q_course') ? 'border-indigo-500/50 bg-slate-800 ring-1 ring-indigo-500/30' : 'border-slate-700/80' }} hover:border-indigo-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Tổng câu hỏi</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-white">{{ $questionsStats['total'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-slate-900 text-indigo-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </span>
                </div>
            </a>

            <!-- Unanswered -->
            <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_status' => 'unanswered', 'q_course' => request('q_course')])) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ request('q_status') === 'unanswered' ? 'border-amber-500/50 bg-slate-800 ring-1 ring-amber-500/30' : 'border-slate-700/80' }} hover:border-amber-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Chờ giải đáp</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-amber-400">{{ $questionsStats['unanswered'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-amber-500/10 text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
            </a>

            <!-- Answered -->
            <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_status' => 'answered', 'q_course' => request('q_course')])) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ request('q_status') === 'answered' ? 'border-emerald-500/50 bg-slate-800 ring-1 ring-emerald-500/30' : 'border-slate-700/80' }} hover:border-emerald-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Đã được giải đáp</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-emerald-400">{{ $questionsStats['answered'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-emerald-500/10 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </span>
                </div>
            </a>

            <!-- My Questions -->
            <a href="{{ route('student.assignments.index', ['tab' => 'questions', 'only_mine' => 1]) }}" 
               class="p-4 rounded-xl bg-slate-800/80 border {{ request('only_mine') ? 'border-sky-500/50 bg-slate-800 ring-1 ring-sky-500/30' : 'border-slate-700/80' }} hover:border-sky-500/50 transition">
                <span class="text-xs text-slate-400 block mb-1">Câu hỏi của tôi</span>
                <div class="flex items-center justify-between">
                    <span class="text-2xl font-bold font-mono text-sky-400">{{ $questionsStats['my_questions'] ?? 0 }}</span>
                    <span class="p-2 rounded-lg bg-sky-500/10 text-sky-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </span>
                </div>
            </a>
        </div>

        <!-- Filters Form for Questions -->
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg space-y-4">
            <!-- 1. Lọc theo Khóa học / Ngoài bài học -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                        <span>🎯 Phạm vi câu hỏi:</span>
                    </span>
                    @if(request('q_course') || request('q_status') || request('q_search') || request('only_mine'))
                        <a href="{{ route('student.assignments.index', ['tab' => 'questions']) }}" class="text-[11px] text-sky-400 hover:text-sky-300">
                            ✕ Xóa toàn bộ bộ lọc
                        </a>
                    @endif
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <!-- Tất cả -->
                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_status' => request('q_status'), 'only_mine' => request('only_mine'), 'q_search' => request('q_search')])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('q_course') ? 'bg-indigo-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                        Tất cả phạm vi
                    </a>

                    <!-- Ngoài bài học -->
                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_course' => 'outside', 'q_status' => request('q_status'), 'only_mine' => request('only_mine'), 'q_search' => request('q_search')])) }}" 
                       class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ request('q_course') === 'outside' ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-900 text-purple-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                        <span>🌐 Ngoài bài học (Hỏi chung)</span>
                        <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('q_course') === 'outside' ? 'bg-purple-800 text-purple-100' : 'bg-slate-800 text-purple-400' }}">
                            {{ $questionsStats['outside'] ?? 0 }}
                        </span>
                    </a>

                    <!-- Từng khóa học -->
                    @foreach($enrolledCourses as $c)
                        <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_course' => $c->id, 'q_status' => request('q_status'), 'only_mine' => request('only_mine'), 'q_search' => request('q_search')])) }}" 
                           class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1.5 {{ request('q_course') == $c->id ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                            <span>📚 {{ Str::limit($c->title, 30) }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- 2. Lọc theo Trạng thái & Ô Tìm kiếm -->
            <form method="GET" action="{{ route('student.assignments.index') }}" class="pt-3 border-t border-slate-700/60 flex flex-col md:flex-row md:items-center justify-between gap-3">
                <input type="hidden" name="tab" value="questions">
                @if(request('q_course'))
                    <input type="hidden" name="q_course" value="{{ request('q_course') }}">
                @endif

                <!-- Trạng thái buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs text-slate-400 mr-1 font-semibold uppercase">Trạng thái:</span>
                    
                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_course' => request('q_course'), 'only_mine' => request('only_mine'), 'q_search' => request('q_search')])) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-medium transition {{ !request('q_status') ? 'bg-slate-700 text-white' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        Tất cả
                    </a>
                    
                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_course' => request('q_course'), 'q_status' => 'unanswered', 'only_mine' => request('only_mine'), 'q_search' => request('q_search')])) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-medium transition {{ request('q_status') === 'unanswered' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        ⏳ Chưa có trả lời ({{ $questionsStats['unanswered'] ?? 0 }})
                    </a>

                    <a href="{{ route('student.assignments.index', array_filter(['tab' => 'questions', 'q_course' => request('q_course'), 'q_status' => 'answered', 'only_mine' => request('only_mine'), 'q_search' => request('q_search')])) }}" 
                       class="px-2.5 py-1 rounded-lg text-xs font-medium transition {{ request('q_status') === 'answered' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                        ✅ Đã có trả lời ({{ $questionsStats['answered'] ?? 0 }})
                    </a>

                    <!-- Toggle only mine -->
                    <label class="inline-flex items-center gap-1.5 ml-2 text-xs text-slate-300 cursor-pointer select-none">
                        <input type="checkbox" name="only_mine" value="1" {{ request('only_mine') ? 'checked' : '' }} onchange="this.form.submit()"
                               class="rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-indigo-500 focus:ring-offset-slate-900">
                        <span>Chỉ câu hỏi của tôi</span>
                    </label>
                </div>

                <!-- Ô tìm kiếm từ khóa -->
                <div class="flex items-center gap-2">
                    @if(request('q_status'))
                        <input type="hidden" name="q_status" value="{{ request('q_status') }}">
                    @endif
                    <div class="relative w-full md:w-64">
                        <input type="text" name="q_search" value="{{ request('q_search') }}" 
                               placeholder="Tìm theo tiêu đề, nội dung..."
                               class="w-full pl-8 pr-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                        <svg class="w-4 h-4 text-slate-500 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-indigo-600 hover:bg-indigo-500 text-white transition">
                        Tìm
                    </button>
                </div>
            </form>
        </div>

        <!-- DANH SÁCH CÂU HỎI -->
        <div class="space-y-3">
            @forelse($questions as $question)
                <div class="p-4 sm:p-5 rounded-xl bg-slate-800/90 border {{ $question->is_featured ? 'border-amber-500/40 bg-gradient-to-r from-amber-950/10 to-slate-800/90' : 'border-slate-700/80' }} hover:border-indigo-500/50 shadow-md transition-all space-y-3">
                    <!-- Badges & Author Row -->
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Scope badge -->
                            @if($question->isOutsideLesson())
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20 flex items-center gap-1">
                                    <span>🌐 Ngoài bài học</span>
                                    @if($question->course)
                                        <span class="text-slate-400">• Khóa: {{ Str::limit($question->course->title, 25) }}</span>
                                    @else
                                        <span class="text-slate-400">• Hỏi đáp chung</span>
                                    @endif
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-sky-500/10 text-sky-400 border border-sky-500/20 flex items-center gap-1">
                                    <span>📚 Bài: {{ Str::limit($question->lesson?->title ?? 'Bài học', 30) }}</span>
                                    @if($question->course || $question->lesson?->course)
                                        <span class="text-slate-400">({{ Str::limit($question->course?->title ?? $question->lesson?->course?->title, 20) }})</span>
                                    @endif
                                </span>
                            @endif

                            <!-- Status badge -->
                            @if($question->answers_count > 0)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    <span>{{ $question->answers_count }} phản hồi</span>
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/20 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                                    <span>Đang chờ giải đáp</span>
                                </span>
                            @endif

                            <!-- Featured badge -->
                            @if($question->is_featured)
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40 flex items-center gap-1">
                                    ⭐ Tiêu biểu
                                </span>
                            @endif

                            <!-- Owner badge -->
                            @if($question->user_id === auth()->id())
                                <span class="px-2 py-0.5 rounded-md text-[11px] font-medium bg-slate-700/80 text-sky-300">
                                    Của bạn
                                </span>
                            @endif
                        </div>

                        <!-- Time -->
                        <span class="text-xs text-slate-500">
                            {{ $question->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <!-- Title & Content -->
                    <div>
                        <a href="{{ route('student.questions.show', $question->id) }}" 
                           class="text-base font-bold text-white hover:text-indigo-400 transition block">
                            {{ $question->title ?: 'Câu hỏi thắc mắc #' . $question->id }}
                        </a>
                        <p class="text-sm text-slate-400 line-clamp-2 mt-1">
                            {{ Str::limit(strip_tags($question->content), 180) }}
                        </p>
                    </div>

                    <!-- Image Thumbnails Preview -->
                    @if(!empty($question->images) && is_array($question->images))
                        <div class="flex items-center gap-2 pt-1">
                            @foreach(array_slice($question->images, 0, 4) as $imgUrl)
                                <a href="{{ route('student.questions.show', $question->id) }}" class="block w-14 h-14 rounded-lg overflow-hidden border border-slate-700 bg-slate-900 hover:border-indigo-500 transition">
                                    <img src="{{ $imgUrl }}" alt="Attachment" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                            @if(count($question->images) > 4)
                                <span class="text-xs text-slate-500 font-medium">+{{ count($question->images) - 4 }} ảnh</span>
                            @endif
                        </div>
                    @endif

                    <!-- Footer: Author Info & CTA -->
                    <div class="pt-3 border-t border-slate-700/50 flex items-center justify-between text-xs">
                        <div class="flex items-center gap-2 text-slate-400">
                            <span class="w-5 h-5 rounded-full bg-slate-700 flex items-center justify-center font-bold text-[10px] text-white">
                                {{ strtoupper(substr($question->user->name ?? 'U', 0, 1)) }}
                            </span>
                            <span>{{ $question->user->name ?? 'Học viên' }}</span>
                            @if($question->user && $question->user->role === 'admin')
                                <span class="px-1.5 py-0.2 rounded text-[10px] bg-amber-500/20 text-amber-300 font-semibold">Giảng viên</span>
                            @endif
                        </div>

                        <a href="{{ route('student.questions.show', $question->id) }}" 
                           class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-400 hover:text-indigo-300 transition">
                            <span>Xem chi tiết & thảo luận</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            @empty
                <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-12 text-center text-slate-500">
                    <svg class="w-12 h-12 mx-auto mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    <p class="text-sm font-semibold text-slate-300">Chưa có câu hỏi nào trong danh sách</p>
                    <p class="text-xs text-slate-500 mt-1">Bạn có thắc mắc về bài học hoặc thị trường? Hãy nhấn nút "Đặt câu hỏi mới" ở trên để gửi thắc mắc tới Giảng viên.</p>
                </div>
            @endforelse

            <!-- Pagination -->
            @if($questions->hasPages())
                <div class="pt-4">
                    {{ $questions->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- ======================================================== -->
    <!-- MODAL ĐẶT CÂU HỎI MỚI (TRONG BÀI HỌC HOẶC NGOÀI BÀI HỌC)  -->
    <!-- ======================================================== -->
    <div x-show="openModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="openModal = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="w-full max-w-2xl bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden">
            
            <!-- Modal Header -->
            <div class="px-6 py-4 bg-slate-800/80 border-b border-slate-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                    </span>
                    <h3 class="text-base font-bold text-white">Đặt Câu Hỏi & Thắc Mắc</h3>
                </div>
                <button type="button" @click="openModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-slate-700 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Form -->
            <form action="{{ route('student.questions.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
                @csrf

                <!-- Scope Selection -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Phạm vi câu hỏi <span class="text-rose-400">*</span></label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-3"
                               :class="scope === 'lesson' ? 'bg-indigo-950/30 border-indigo-500 text-white ring-1 ring-indigo-500/30' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:border-slate-600'">
                            <input type="radio" name="scope" value="lesson" x-model="scope" class="mt-0.5 text-indigo-600 focus:ring-indigo-500 bg-slate-900 border-slate-700">
                            <div>
                                <span class="text-sm font-bold block" :class="scope === 'lesson' ? 'text-indigo-300' : 'text-slate-300'">📚 Trong bài học cụ thể</span>
                                <span class="text-xs text-slate-400 block mt-0.5">Thắc mắc nội dung hoặc kiến thức của một bài giảng trong khóa học.</span>
                            </div>
                        </label>

                        <label class="p-3 rounded-xl border cursor-pointer transition flex items-start gap-3"
                               :class="scope === 'outside' ? 'bg-purple-950/30 border-purple-500 text-white ring-1 ring-purple-500/30' : 'bg-slate-800/80 border-slate-700 text-slate-400 hover:border-slate-600'">
                            <input type="radio" name="scope" value="outside" x-model="scope" class="mt-0.5 text-purple-600 focus:ring-purple-500 bg-slate-900 border-slate-700">
                            <div>
                                <span class="text-sm font-bold block" :class="scope === 'outside' ? 'text-purple-300' : 'text-slate-300'">🌐 Ngoài bài học (Hỏi chung)</span>
                                <span class="text-xs text-slate-400 block mt-0.5">Câu hỏi về tâm lý, thị trường, setup thực tế hoặc ngoài giáo trình.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Scope: Lesson -> Chọn Khóa học + Chọn Bài học -->
                <div x-show="scope === 'lesson'" class="space-y-3 p-4 rounded-xl bg-slate-800/50 border border-slate-700/60">
                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">1. Chọn Khóa học <span class="text-rose-400">*</span></label>
                        <select x-model="selectedCourseId" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="">-- Vui lòng chọn khóa học --</option>
                            @foreach($enrolledCoursesWithLessons as $c)
                                <option value="{{ $c->id }}">{{ $c->title }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-300 mb-1">2. Chọn Bài học <span class="text-rose-400">*</span></label>
                        <select name="lesson_id" x-model="selectedLessonId" :disabled="scope !== 'lesson' || !selectedCourseId"
                                class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-sm focus:border-indigo-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed">
                            <option value="">-- Vui lòng chọn bài học --</option>
                            <template x-for="lesson in availableLessons" :key="lesson.id">
                                <option :value="lesson.id" x-text="lesson.title"></option>
                            </template>
                        </select>
                        <p x-show="selectedCourseId && availableLessons.length === 0" class="text-xs text-amber-400 mt-1">Khóa học này hiện chưa có bài học nào.</p>
                    </div>
                </div>

                <!-- Scope: Outside -> Tùy chọn liên quan đến khóa học nào -->
                <div x-show="scope === 'outside'" class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/60 space-y-1">
                    <label class="block text-xs font-medium text-slate-300 mb-1">Liên quan đến khóa học (Tùy chọn):</label>
                    <select name="course_id" :disabled="scope !== 'outside'" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-sm focus:border-purple-500 focus:outline-none">
                        <option value="">-- Không gắn khóa cụ thể (Hỏi đáp chung thị trường / phương pháp) --</option>
                        @foreach($enrolledCourses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                    <p class="text-[11px] text-slate-400">Nếu câu hỏi thắc mắc liên quan tới chủ đề của một khóa học cụ thể, hãy chọn khóa học đó.</p>
                </div>

                <!-- Title -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Tiêu đề câu hỏi</label>
                    <input type="text" name="title" value="{{ old('title') }}" 
                           placeholder="Ví dụ: Chưa rõ cách xác định Liquidity Sweep ở khung H1..."
                           class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-indigo-500">
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Nội dung thắc mắc chi tiết <span class="text-rose-400">*</span></label>
                    <textarea name="content" rows="4" required
                              placeholder="Mô tả cụ thể câu hỏi hoặc tình huống giao dịch bạn đang gặp phải để Giảng viên hỗ trợ giải đáp chính xác nhất..."
                              class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-sm focus:outline-none focus:border-indigo-500">{{ old('content') }}</textarea>
                </div>

                <!-- Images upload -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1">Đính kèm ảnh minh họa (Tối đa 5 ảnh)</label>
                    <input type="file" name="images[]" multiple accept="image/*"
                           class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-indigo-400 hover:file:bg-slate-700 file:cursor-pointer cursor-pointer">
                    <p class="text-[11px] text-slate-500 mt-1">Định dạng hỗ trợ: JPG, PNG, WebP (Tối đa 10MB mỗi ảnh). Hãy đính kèm ảnh biểu đồ để Giảng viên dễ nhận xét.</p>
                </div>

                <!-- Footer buttons -->
                <div class="pt-4 border-t border-slate-700/80 flex items-center justify-end gap-3">
                    <button type="button" @click="openModal = false" class="px-4 py-2 rounded-xl text-xs font-medium text-slate-300 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                        Hủy
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-xl text-xs font-semibold bg-gradient-to-r from-indigo-500 to-sky-500 hover:from-indigo-400 hover:to-sky-400 text-white shadow-lg transition">
                        Gửi câu hỏi ngay
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
