@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" class="hover:text-white">Học viên</a>
                <span>/</span>
                <span class="text-emerald-400">Chỉnh sửa</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Chỉnh Sửa Học Viên: {{ $student->name ?? 'Học viên' }}</h1>
            <p class="text-sm text-slate-400">Cập nhật thông tin tài khoản và trạng thái học viên</p>
        </div>
        <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.students.update') ? route('admin.students.update', $student->id ?? 1) : url('/admin/students/' . ($student->id ?? 1)) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="sm:col-span-2">
                    <label for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Họ và tên học viên <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name', $student->name ?? '') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('name') border-rose-500 @enderror"
                    />
                    @error('name')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Địa chỉ Email <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        value="{{ old('email', $student->email ?? '') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('email') border-rose-500 @enderror"
                    />
                    @error('email')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Số điện thoại
                    </label>
                    <input 
                        type="text" 
                        name="phone" 
                        id="phone" 
                        value="{{ old('phone', $student->phone ?? '') }}" 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('phone') border-rose-500 @enderror"
                    />
                    @error('phone')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password (Optional) -->
                <div>
                    <label for="password" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Mật khẩu mới (để trống nếu không đổi)
                    </label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        placeholder="••••••••" 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('password') border-rose-500 @enderror"
                    />
                    @error('password')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Xác nhận mật khẩu mới
                    </label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        placeholder="••••••••" 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                    />
                </div>

                <!-- Bio / Note -->
                <div class="sm:col-span-2">
                    <label for="bio" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Tiểu sử / Ghi chú về học viên
                    </label>
                    <textarea 
                        name="bio" 
                        id="bio" 
                        rows="3" 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('bio') border-rose-500 @enderror"
                    >{{ old('bio', $student->bio ?? '') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Is Active Toggle -->
                <div class="sm:col-span-2" x-data="{ active: {{ old('is_active', $student->is_active ?? 1) ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between p-4 rounded-lg bg-slate-900/60 border border-slate-700">
                        <div>
                            <span class="text-sm font-semibold text-white">Trạng thái kích hoạt tài khoản</span>
                            <p class="text-xs text-slate-400">Tắt tùy chọn này sẽ tạm dừng quyền đăng nhập của học viên</p>
                        </div>
                        <input type="hidden" name="is_active" :value="active ? 1 : 0">
                        <button 
                            type="button" 
                            @click="active = !active" 
                            :class="active ? 'bg-emerald-600' : 'bg-slate-700'" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        >
                            <span :class="active ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Cập nhật học viên
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
