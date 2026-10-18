@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <span class="text-emerald-400">Chỉnh sửa</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Chỉnh Sửa Khóa Học</h1>
            <p class="text-sm text-slate-400">{{ $course->title ?? 'Khóa học Forex' }}</p>
        </div>
        <a href="{{ Route::has('admin.courses.show') ? route('admin.courses.show', $course->id) : url('/admin/courses/' . $course->id) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại chi tiết
        </a>
    </div>

    <!-- Form -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.courses.update') ? route('admin.courses.update', $course->id) : url('/admin/courses/' . $course->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tiêu đề khóa học <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $course->title) }}" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('title') border-rose-500 @enderror"
                />
                @error('title')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Mô tả khóa học
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('description') border-rose-500 @enderror"
                >{{ old('description', $course->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thumbnail Upload -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Hình ảnh đại diện (Thumbnail)
                </label>

                @if(!empty($course->thumbnail))
                    <div class="mb-3 flex items-center gap-4 p-3 rounded-lg bg-slate-900 border border-slate-700">
                        <img src="{{ str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="Current thumbnail" class="w-20 h-14 object-cover rounded">
                        <span class="text-xs text-slate-400">Ảnh hiện tại. Tải tệp mới lên nếu muốn đổi.</span>
                    </div>
                @endif

                <input 
                    type="file" 
                    name="thumbnail" 
                    id="thumbnail" 
                    accept="image/*"
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600 cursor-pointer"
                />
                <input 
                    type="url" 
                    name="thumbnail_url" 
                    value="{{ old('thumbnail_url', str_starts_with($course->thumbnail ?? '', 'http') ? $course->thumbnail : '') }}"
                    placeholder="Hoặc nhập link ảnh trực tiếp: https://..."
                    class="mt-2 w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                />
                @error('thumbnail')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Sort Order & Published -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Thứ tự hiển thị (#)
                    </label>
                    <input 
                        type="number" 
                        name="sort_order" 
                        id="sort_order" 
                        value="{{ old('sort_order', $course->sort_order ?? 1) }}" 
                        min="1" 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('sort_order') border-rose-500 @enderror"
                    />
                    @error('sort_order')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Published Toggle -->
                <div x-data="{ published: {{ old('is_published', $course->is_published ?? 1) ? 'true' : 'false' }} }">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Trạng thái phát hành
                    </label>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border border-slate-700">
                        <span class="text-xs text-slate-300" x-text="published ? 'Đang xuất bản công khai' : 'Lưu chế độ bản nháp'"></span>
                        <input type="hidden" name="is_published" :value="published ? 1 : 0">
                        <button 
                            type="button" 
                            @click="published = !published" 
                            :class="published ? 'bg-emerald-600' : 'bg-slate-700'" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        >
                            <span :class="published ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Permission / Student Enrollment -->
            <div class="pt-4 border-t border-slate-700/60">
                <h3 class="text-sm font-bold text-white flex items-center gap-2 mb-1">
                    <span>👥 Học viên được cấp quyền truy cập</span>
                    <span class="text-xs font-normal text-emerald-400">({{ count($enrolledStudentIds ?? []) }} học viên đang có quyền)</span>
                </h3>
                <p class="text-xs text-slate-400 mb-3">Tích chọn để cấp quyền hoặc bỏ tích để thu hồi quyền học của học viên</p>

                @if(isset($students) && count($students) > 0)
                    <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-700 max-h-56 overflow-y-auto space-y-2">
                        @foreach($students as $st)
                            <label class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-800 transition cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="checkbox" 
                                        name="student_ids[]" 
                                        value="{{ $st->id }}"
                                        {{ in_array($st->id, old('student_ids', $enrolledStudentIds ?? [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500 bg-slate-800 border-slate-700"
                                    >
                                    <div>
                                        <div class="text-xs font-semibold text-white">{{ $st->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $st->email }}</div>
                                    </div>
                                </div>
                                <span class="text-[11px] px-2 py-0.5 rounded font-mono {{ in_array($st->id, $enrolledStudentIds ?? []) ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-500' }}">
                                    {{ in_array($st->id, $enrolledStudentIds ?? []) ? 'Đang có quyền' : 'Chưa cấp quyền' }}
                                </span>
                            </label>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('admin.courses.show') ? route('admin.courses.show', $course->id) : url('/admin/courses/' . $course->id) }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Cập nhật khóa học
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
