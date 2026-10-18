@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📚 Quản Lý Khóa Học Forex</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Danh sách giáo án đào tạo, quản lý bài học và cấp quyền học viên tham gia</p>
        </div>
        <a href="{{ Route::has('admin.courses.create') ? route('admin.courses.create') : url('/admin/courses/create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tạo khóa học mới
        </a>
    </div>

    <!-- Filter Bar -->
    <div class="rounded-xl bg-slate-800/80 border border-slate-700/80 p-4 shadow-sm">
        <form method="GET" action="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm tiêu đề hoặc mô tả khóa học..." 
                       class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs placeholder:text-slate-500 focus:outline-none focus:border-emerald-500">
            </div>

            <div>
                <select name="status" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đang xuất bản</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp (Tạm ẩn)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold transition">
                    Lọc dữ liệu
                </button>
                @if(request()->hasAny(['search', 'status']))
                    <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" 
                       class="px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 text-xs transition">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Courses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($courses ?? [] as $course)
            <div class="group rounded-xl bg-slate-800/90 border border-slate-700/80 overflow-hidden shadow-lg hover:border-slate-600 transition-all flex flex-col">
                <!-- Thumbnail -->
                <div class="relative h-44 bg-slate-950 overflow-hidden">
                    @if(!empty($course->thumbnail))
                        <img src="{{ str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-900 via-slate-800 to-emerald-950/40 p-4">
                            <svg class="w-12 h-12 text-slate-600 group-hover:text-emerald-500/50 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    @endif
                    <!-- Published Badge -->
                    <div class="absolute top-3 right-3 flex items-center gap-1.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold backdrop-blur-md {{ ($course->is_published ?? true) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-800/80 text-slate-400 border border-slate-700' }}">
                            {{ ($course->is_published ?? true) ? 'Đang xuất bản' : 'Bản nháp' }}
                        </span>
                    </div>

                    <!-- Sort order badge -->
                    <div class="absolute top-3 left-3">
                        <span class="px-2 py-0.5 rounded text-xs font-mono font-bold bg-slate-950/80 text-white border border-slate-700 backdrop-blur-sm">
                            Thứ tự #{{ $course->sort_order ?? 1 }}
                        </span>
                    </div>
                </div>

                <!-- Content -->
                <div class="p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-3 text-xs mb-2">
                            <span class="inline-flex items-center gap-1 text-sky-400 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                {{ $course->lessons_count ?? (isset($course->lessons) ? count($course->lessons) : 0) }} bài học
                            </span>
                            <span>•</span>
                            <span class="inline-flex items-center gap-1 text-emerald-400 font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                {{ $course->enrolled_students_count ?? (isset($course->enrolledStudents) ? count($course->enrolledStudents) : 0) }} học viên được cấp quyền
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-white group-hover:text-emerald-400 transition-colors line-clamp-1">
                            {{ $course->title }}
                        </h3>
                        <p class="mt-2 text-xs text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $course->description ?? 'Chưa có mô tả khóa học.' }}
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="mt-5 pt-4 border-t border-slate-700/60 flex items-center justify-between">
                        <a href="{{ Route::has('admin.courses.show') ? route('admin.courses.show', $course->id) : url('/admin/courses/' . $course->id) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 text-xs font-semibold transition border border-emerald-500/30">
                            <span>Quản lý bài & cấp quyền</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>

                        <div class="flex items-center gap-1">
                            <!-- Toggle Publish Status -->
                            <form method="POST" action="{{ Route::has('admin.courses.toggle') ? route('admin.courses.toggle', $course->id) : url('/admin/courses/' . $course->id . '/toggle') }}" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-sky-400 hover:bg-slate-700/60" title="{{ $course->is_published ? 'Chuyển về bản nháp' : 'Xuất bản khóa học' }}">
                                    @if($course->is_published)
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                    @endif
                                </button>
                            </form>

                            <a href="{{ Route::has('admin.courses.edit') ? route('admin.courses.edit', $course->id) : url('/admin/courses/' . $course->id . '/edit') }}" 
                               class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-700/60" title="Chỉnh sửa">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            <form method="POST" action="{{ Route::has('admin.courses.destroy') ? route('admin.courses.destroy', $course->id) : url('/admin/courses/' . $course->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa khóa học này? Mọi bài học bên trong sẽ bị xóa theo.')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-700/60" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center text-slate-500 rounded-xl bg-slate-800/60 border border-slate-700">
                <svg class="w-16 h-16 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                <p class="text-sm font-semibold text-slate-400">Chưa có khóa học nào được tạo</p>
                <p class="text-xs text-slate-500 mt-1">Bấm "Tạo khóa học mới" để bắt đầu soạn giáo trình cho học viên</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if(isset($courses) && method_exists($courses, 'hasPages') && $courses->hasPages())
        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    @endif
</div>
@endsection
