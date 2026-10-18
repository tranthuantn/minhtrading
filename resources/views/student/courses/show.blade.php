@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('student.courses.index') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <span class="text-sky-400">Danh sách bài học</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $course->title }}</h1>
            <p class="text-sm text-slate-400 mt-1">{{ $course->description ?? 'Chưa có mô tả chi tiết.' }}</p>
        </div>

        <a href="{{ route('student.courses.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Tất cả khóa học
        </a>
    </div>

    <!-- Course Overview & Lessons List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Lessons List (2 cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
                <div class="p-4 border-b border-slate-700/60 flex items-center justify-between bg-slate-900/60">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>📑 Giáo Trình Bài Học Theo Lộ Trình</span>
                    </h3>
                    <span class="text-xs text-sky-400 font-mono font-semibold">{{ count($lessons ?? []) }} bài học</span>
                </div>

                <div class="divide-y divide-slate-700/50">
                    @forelse($lessons ?? [] as $lesson)
                        <a href="{{ route('student.courses.lesson', ['course' => $course->id, 'lesson' => $lesson->id]) }}" 
                           class="p-4 flex items-center justify-between hover:bg-slate-750 transition-colors group">
                            <div class="flex items-center gap-3.5">
                                <span class="w-8 h-8 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-xs font-bold font-mono text-sky-400 group-hover:border-sky-500">
                                    {{ $lesson->sort_order }}
                                </span>
                                <div>
                                    <h4 class="text-sm font-semibold text-white group-hover:text-sky-400 transition-colors">
                                        {{ $lesson->title }}
                                    </h4>
                                    <div class="flex items-center gap-3 text-xs text-slate-400 mt-1">
                                        @if(!empty($lesson->video_url))
                                            <span class="inline-flex items-center gap-1 text-sky-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                Video bài giảng
                                            </span>
                                        @endif
                                        @if(!empty($lesson->attachments) && count($lesson->attachments) > 0)
                                            <span class="inline-flex items-center gap-1 text-amber-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                {{ count($lesson->attachments) }} file tài liệu
                                            </span>
                                        @endif
                                        @if($lesson->assignments_count > 0)
                                            <span class="inline-flex items-center gap-1 text-purple-400">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                                {{ $lesson->assignments_count }} bài tập
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <span class="inline-flex items-center gap-1 text-xs font-semibold text-sky-400 group-hover:translate-x-1 transition-transform">
                                Học bài &rarr;
                            </span>
                        </a>
                    @empty
                        <div class="p-8 text-center text-slate-500">
                            Chưa có bài học nào được xuất bản trong khóa học này.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-4">
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-4">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>🎓 Thông Tin Khóa Học</span>
                </h3>
                @if(!empty($course->thumbnail))
                    <img src="{{ str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-36 object-cover rounded-lg border border-slate-700">
                @endif
                <div class="space-y-2 text-xs text-slate-300">
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-700/60">
                        <span class="text-slate-400">Trạng thái:</span>
                        <span class="text-emerald-400 font-semibold">✓ Đã cấp quyền học</span>
                    </div>
                    <div class="flex items-center justify-between py-1.5 border-b border-slate-700/60">
                        <span class="text-slate-400">Số bài học:</span>
                        <span class="text-white font-mono font-bold">{{ count($lessons ?? []) }} bài</span>
                    </div>
                </div>

                @if(isset($lessons) && count($lessons) > 0)
                    <div class="pt-2">
                        <a href="{{ route('student.courses.lesson', ['course' => $course->id, 'lesson' => $lessons->first()->id]) }}" 
                           class="w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white font-semibold text-xs shadow-lg shadow-sky-600/20 transition-all">
                            <span>Bắt đầu bài 1 ngay</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
