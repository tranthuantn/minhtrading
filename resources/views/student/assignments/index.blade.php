@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📝 Bài Tập & Bài Nộp Theo Khóa Học</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Mỗi khóa học có số lượng bài tập tương ứng với các bài học. Quản lý tiến độ làm bài, nộp bài và nhận xét của Giảng viên</p>
        </div>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <!-- Total -->
        <a href="{{ route('student.assignments.index', array_filter(['course_id' => $selectedCourseId])) }}" 
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
        <a href="{{ route('student.assignments.index', array_filter(['status' => 'not_submitted', 'course_id' => $selectedCourseId])) }}" 
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
        <a href="{{ route('student.assignments.index', array_filter(['status' => 'pending', 'course_id' => $selectedCourseId])) }}" 
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
        <a href="{{ route('student.assignments.index', array_filter(['status' => 'reviewed', 'course_id' => $selectedCourseId])) }}" 
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
                    <a href="{{ route('student.assignments.index') }}" class="text-[11px] text-sky-400 hover:text-sky-300">
                        ✕ Xóa toàn bộ bộ lọc
                    </a>
                @endif
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('student.assignments.index', array_filter(['status' => request('status')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !$selectedCourseId ? 'bg-sky-600 text-white shadow-sm' : 'bg-slate-900 text-slate-300 hover:bg-slate-700/80 border border-slate-700/60' }}">
                    📚 Tất cả khóa học ({{ $enrolledCourses->count() }})
                </a>
                @foreach($enrolledCourses as $c)
                    <a href="{{ route('student.assignments.index', array_filter(['course_id' => $c->id, 'status' => request('status')])) }}" 
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
                <a href="{{ route('student.assignments.index', array_filter(['course_id' => $selectedCourseId])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                    Tất cả ({{ $stats['total'] ?? 0 }})
                </a>

                <a href="{{ route('student.assignments.index', array_filter(['status' => 'not_submitted', 'course_id' => $selectedCourseId])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'not_submitted' ? 'bg-amber-500/20 text-amber-400 border border-amber-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                    Chưa nộp ({{ $stats['not_submitted'] ?? 0 }})
                </a>

                <a href="{{ route('student.assignments.index', array_filter(['status' => 'pending', 'course_id' => $selectedCourseId])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'pending' ? 'bg-sky-500/20 text-sky-400 border border-sky-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                    Chờ chấm ({{ $stats['pending'] ?? 0 }})
                </a>

                <a href="{{ route('student.assignments.index', array_filter(['status' => 'reviewed', 'course_id' => $selectedCourseId])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ request('status') === 'reviewed' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                    Đã chấm điểm ({{ $stats['reviewed'] ?? 0 }})
                </a>

                @if(($stats['revision_needed'] ?? 0) > 0)
                    <a href="{{ route('student.assignments.index', array_filter(['status' => 'revision_needed', 'course_id' => $selectedCourseId])) }}" 
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
                    <div class="w-10 h-10 rounded-lg bg-sky-500/15 border border-sky-500/30 flex items-center justify-center text-sky-400 font-bold text-lg shrink-0">
                        📚
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-base font-bold text-white">{{ $course->title }}</h2>
                            <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                                {{ $cStats['total_assignments'] }} bài tập / {{ $course->lessons_count }} bài học
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5 line-clamp-1">{{ $course->description }}</p>
                    </div>
                </div>

                <!-- Tiến độ hoàn thành bài tập của khóa -->
                <div class="flex items-center gap-4 bg-slate-950/60 px-3.5 py-2 rounded-lg border border-slate-800 shrink-0">
                    <div>
                        <div class="text-[11px] text-slate-400">Tiến độ bài tập:</div>
                        <div class="text-xs font-semibold text-white">
                            <span class="text-emerald-400 font-bold font-mono">{{ $cStats['completed'] }}</span> / {{ $cStats['total_assignments'] }} bài hoàn thành
                        </div>
                    </div>
                    <div class="w-24">
                        <div class="flex items-center justify-between text-[10px] text-slate-400 mb-1">
                            <span>{{ $cStats['percent'] }}%</span>
                        </div>
                        <div class="w-full bg-slate-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $cStats['percent'] }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bảng bài tập của khóa học -->
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-900/40 text-[11px] font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700/60">
                            <th class="py-3 px-4 min-w-[260px]">Bài tập & Bài học tương ứng</th>
                            <th class="py-3 px-4">Loại bài</th>
                            <th class="py-3 px-4">Hạn nộp</th>
                            <th class="py-3 px-4 min-w-[200px]">Trạng thái nộp bài & Điểm số</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/40">
                        @forelse($courseAssignments as $assignment)
                            @php
                                $sub = $assignment->submissions->first() ?? null;
                                $latestReview = $sub ? $sub->reviews->last() : null;
                                $score = $sub->score ?? ($latestReview->score ?? null);
                                $status = $sub->status ?? 'not_submitted';
                            @endphp
                            <tr class="hover:bg-slate-750/30 transition-colors">
                                <!-- Title & Lesson -->
                                <td class="py-3.5 px-4">
                                    <a href="{{ route('student.assignments.show', $assignment->id) }}" 
                                       class="font-semibold text-white hover:text-sky-400 transition-colors block text-sm">
                                        {{ $assignment->title }}
                                    </a>
                                    <div class="flex items-center gap-2 mt-1">
                                        <span class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                                            <svg class="w-3.5 h-3.5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                            {{ $assignment->lesson->title ?? 'Bài học' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Type -->
                                <td class="py-3.5 px-4 whitespace-nowrap">
                                    @if($assignment->type === 'backtest')
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-medium border bg-indigo-500/10 text-indigo-400 border-indigo-500/30">
                                            📊 Backtest
                                        </span>
                                    @elseif($assignment->type === 'trade_entry')
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-medium border bg-emerald-500/10 text-emerald-400 border-emerald-500/30">
                                            💹 Vào lệnh
                                        </span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[11px] font-mono font-medium border bg-sky-500/10 text-sky-400 border-sky-500/30">
                                            🔍 Phân tích Chart
                                        </span>
                                    @endif
                                </td>

                                <!-- Deadline -->
                                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-slate-400 text-[11px]">
                                    {{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : 'Không giới hạn' }}
                                </td>

                                <!-- Submission Status & Score -->
                                <td class="py-3.5 px-4">
                                    @if(!$sub)
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-amber-500/10 text-amber-400 border border-amber-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Chưa nộp bài
                                        </span>
                                    @elseif($status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                                            <span class="w-1.5 h-1.5 rounded-full bg-sky-400 animate-pulse"></span>
                                            Đã nộp, chờ chấm
                                        </span>
                                        <span class="text-[10px] text-slate-500 block mt-0.5 font-mono">
                                            Gửi lúc: {{ $sub->created_at ? $sub->created_at->format('d/m/Y H:i') : '' }}
                                        </span>
                                    @elseif(in_array($status, ['reviewed', 'approved']))
                                        <div class="flex items-center gap-2">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                                ✓ Đã chấm: {{ number_format($score ?? 10, 1) }}/10
                                            </span>
                                        </div>
                                        @if($latestReview && !empty($latestReview->feedback))
                                            <p class="text-[11px] text-slate-400 truncate max-w-xs mt-0.5" title="{{ $latestReview->feedback }}">
                                                "{{ Str::limit($latestReview->feedback, 45) }}"
                                            </p>
                                        @endif
                                    @elseif($status === 'revision_needed')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                            ⚠️ Cần sửa lại
                                        </span>
                                        @if($latestReview && !empty($latestReview->feedback))
                                            <p class="text-[11px] text-rose-300/80 truncate max-w-xs mt-0.5" title="{{ $latestReview->feedback }}">
                                                {{ Str::limit($latestReview->feedback, 45) }}
                                            </p>
                                        @endif
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
</div>
@endsection
