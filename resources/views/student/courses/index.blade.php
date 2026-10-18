@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📚 Khóa Học Của Tôi</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/30">
                    Được Cấp Quyền Học
                </span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Các chương trình đào tạo Forex mà bạn đã được Giảng viên cấp quyền tham gia học tập</p>
        </div>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses ?? [] as $course)
            <div class="group rounded-xl bg-slate-800/90 border border-slate-700/80 overflow-hidden shadow-lg hover:border-sky-500/50 transition-all flex flex-col">
                <!-- Thumbnail -->
                <div class="relative h-48 bg-slate-950 overflow-hidden">
                    @if(!empty($course->thumbnail))
                        <img src="{{ str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-sky-950/40 p-4">
                            <svg class="w-12 h-12 text-slate-600 group-hover:text-sky-400/60 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    <div class="absolute bottom-3 left-3">
                        <span class="px-2.5 py-1 rounded-md text-xs font-semibold backdrop-blur-md bg-slate-900/80 text-sky-400 border border-sky-500/30">
                            {{ $course->lessons_count ?? (isset($course->lessons) ? count($course->lessons) : 0) }} bài học
                        </span>
                    </div>

                    <div class="absolute top-3 right-3">
                        <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40 backdrop-blur-sm">
                            ✓ Đã mở khóa
                        </span>
                    </div>
                </div>

                <!-- Info -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-bold text-white group-hover:text-sky-400 transition-colors line-clamp-1">
                            {{ $course->title }}
                        </h3>
                        <p class="mt-2 text-xs text-slate-400 line-clamp-3 leading-relaxed">
                            {{ $course->description ?? 'Nắm bắt toàn bộ kiến thức về cấu trúc thị trường, phân tích nến và các vùng cản quan trọng.' }}
                        </p>
                    </div>

                    <div class="mt-5 pt-4 border-t border-slate-700/60 flex items-center justify-between">
                        <span class="text-xs text-emerald-400 font-medium">Quyền truy cập: Toàn khóa</span>
                        <a href="{{ route('student.courses.show', $course->id) }}" 
                           class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow transition-all">
                            Vào học ngay
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 px-6 text-center rounded-2xl bg-slate-800/60 border border-slate-700/80">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <h3 class="text-base font-bold text-white mb-1">Bạn chưa được cấp quyền truy cập khóa học nào</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                    Hệ thống giới hạn nội dung học tập theo quyền của từng học viên. Vui lòng liên hệ với Giảng viên hoặc quản trị viên để được mở quyền truy cập khóa học.
                </p>
            </div>
        @endforelse
    </div>

    @if(isset($courses) && method_exists($courses, 'hasPages') && $courses->hasPages())
        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
