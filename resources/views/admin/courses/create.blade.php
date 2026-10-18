@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <span class="text-emerald-400">Tạo khóa học mới</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Tạo Khóa Học Mới</span>
            </h1>
            <p class="text-sm text-slate-400">Thiết lập chương trình đào tạo và cấp quyền tham gia cho học viên</p>
        </div>
        <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.courses.store') ? route('admin.courses.store') : url('/admin/courses') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tên khóa học <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}" 
                    required 
                    placeholder="Ví dụ: Smart Money Concepts (SMC) & Dòng Tiền Lớn 2026" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('title') border-rose-500 @enderror"
                />
                @error('title')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Mô tả tổng quan khóa học
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4" 
                    placeholder="Mô tả mục tiêu khóa học, các kiến thức chính sẽ học, phong cách giao dịch và đối tượng học viên..." 
                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('description') border-rose-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Thumbnail & Sort Order -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="thumbnail" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Ảnh đại diện khóa học (Upload file)
                    </label>
                    <input 
                        type="file" 
                        name="thumbnail" 
                        id="thumbnail" 
                        accept="image/*"
                        class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600 cursor-pointer"
                    />
                    <div class="mt-2 text-[11px] text-slate-500">Hoặc nhập link ảnh bên dưới nếu không upload file:</div>
                    <input 
                        type="url" 
                        name="thumbnail_url" 
                        value="{{ old('thumbnail_url') }}"
                        placeholder="https://example.com/banner.jpg"
                        class="mt-1 w-full px-3 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500"
                    />
                    @error('thumbnail')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-4">
                    <div>
                        <label for="sort_order" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Thứ tự hiển thị (#)
                        </label>
                        <input 
                            type="number" 
                            name="sort_order" 
                            id="sort_order" 
                            value="{{ old('sort_order', 1) }}" 
                            min="1" 
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                        />
                    </div>

                    <div x-data="{ published: {{ old('is_published', 1) ? 'true' : 'false' }} }">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Trạng thái xuất bản
                        </label>
                        <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border border-slate-700">
                            <span class="text-xs text-slate-300" x-text="published ? 'Xuất bản công khai' : 'Lưu bản nháp'"></span>
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
            </div>

            <!-- Permission / Student Enrollment -->
            <div class="pt-4 border-t border-slate-700/60">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <span>👥 Cấp quyền cho học viên tham gia</span>
                            <span class="text-xs font-normal text-emerald-400">(Học viên chỉ xem được khóa học khi được cấp quyền)</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Chọn các học viên bạn muốn cho phép truy cập khóa học này ngay khi tạo</p>
                    </div>
                </div>

                @if(isset($students) && count($students) > 0)
                    <div class="mt-3 p-4 rounded-xl bg-slate-900/90 border border-slate-700 max-h-56 overflow-y-auto space-y-2">
                        @foreach($students as $st)
                            <label class="flex items-center justify-between p-2.5 rounded-lg hover:bg-slate-800 transition cursor-pointer">
                                <div class="flex items-center gap-3">
                                    <input 
                                        type="checkbox" 
                                        name="student_ids[]" 
                                        value="{{ $st->id }}"
                                        {{ in_array($st->id, old('student_ids', [])) ? 'checked' : '' }}
                                        class="w-4 h-4 rounded text-emerald-500 focus:ring-emerald-500 bg-slate-800 border-slate-700"
                                    >
                                    <div>
                                        <div class="text-xs font-semibold text-white">{{ $st->name }}</div>
                                        <div class="text-[11px] text-slate-400 font-mono">{{ $st->email }}</div>
                                    </div>
                                </div>
                                <span class="text-[11px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 font-mono">Học viên</span>
                            </label>
                        @endforeach
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-700 text-xs text-slate-400 text-center">
                        Chưa có học viên nào trong hệ thống. Bạn có thể cấp quyền sau trong trang chi tiết khóa học.
                    </div>
                @endif
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Tạo khóa học
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
