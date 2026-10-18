@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : url('/admin/notifications') }}" class="hover:text-white">Thông báo</a>
                <span>/</span>
                <span class="text-emerald-400">Tạo thông báo mới</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Gửi Thông Báo Mới</h1>
            <p class="text-sm text-slate-400">Tạo thông báo gửi tới toàn bộ lớp học hoặc danh sách học viên chỉ định</p>
        </div>
        <a href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : url('/admin/notifications') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.notifications.store') ? route('admin.notifications.store') : url('/admin/notifications') }}" class="space-y-6" x-data="{ recipientType: 'all' }">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tiêu đề thông báo <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}" 
                    required 
                    placeholder="Ví dụ: Lịch học Zoom thực chiến phân tích Forex tối nay 19:30" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('title') border-rose-500 @enderror"
                />
                @error('title')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Type -->
            <div>
                <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Phân loại thông báo <span class="text-rose-400">*</span>
                </label>
                <select 
                    name="type" 
                    id="type" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 text-sm"
                >
                    <option value="info" {{ old('type') == 'info' ? 'selected' : '' }}>ℹ️ Thông tin chung (Lịch học, Zoom, Tài liệu)</option>
                    <option value="warning" {{ old('type') == 'warning' ? 'selected' : '' }}>⚠️ Cảnh báo thị trường (Tin tức mạnh, Quản trị vốn)</option>
                    <option value="assignment" {{ old('type') == 'assignment' ? 'selected' : '' }}>📝 Bài tập & Nhắc nhở nộp bài</option>
                    <option value="urgent" {{ old('type') == 'urgent' ? 'selected' : '' }}>🚨 Khẩn cấp</option>
                </select>
            </div>

            <!-- Message Content -->
            <div>
                <label for="message" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Nội dung thông báo <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="message" 
                    id="message" 
                    rows="6" 
                    required 
                    placeholder="Soạn nội dung chi tiết thông báo..." 
                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 @error('message') border-rose-500 @enderror"
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Recipients Selection -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-2">
                    Người nhận thông báo <span class="text-rose-400">*</span>
                </label>
                <div class="space-y-3">
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-200">
                            <input type="radio" name="recipient_type" value="all" x-model="recipientType" class="text-emerald-500 focus:ring-emerald-500" checked>
                            <span>Gửi tới <strong>Tất cả học viên</strong> trong hệ thống</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer text-sm text-slate-200">
                            <input type="radio" name="recipient_type" value="select" x-model="recipientType" class="text-emerald-500 focus:ring-emerald-500">
                            <span>Chỉ định học viên cụ thể</span>
                        </label>
                    </div>

                    <!-- Individual Students List (Shown when 'select' is chosen) -->
                    <div x-show="recipientType === 'select'" class="p-4 rounded-lg bg-slate-900 border border-slate-700 max-h-48 overflow-y-auto space-y-2" style="display: none;">
                        <p class="text-xs text-slate-400 mb-2">Chọn học viên nhận thông báo:</p>
                        @if(isset($students))
                            @foreach($students as $st)
                                <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer py-1">
                                    <input type="checkbox" name="recipients[]" value="{{ $st->id }}" class="rounded text-emerald-500 bg-slate-800 border-slate-700 focus:ring-emerald-500">
                                    <span>{{ $st->name }} ({{ $st->email }})</span>
                                </label>
                            @endforeach
                        @else
                            <label class="flex items-center gap-2 text-xs text-slate-300">
                                <input type="checkbox" name="recipients[]" value="1" class="rounded text-emerald-500 bg-slate-800 border-slate-700">
                                <span>Vũ Đức Nam (nam.vu@example.com)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-slate-300">
                                <input type="checkbox" name="recipients[]" value="2" class="rounded text-emerald-500 bg-slate-800 border-slate-700">
                                <span>Lê Thuỳ Dung (dung.le@example.com)</span>
                            </label>
                            <label class="flex items-center gap-2 text-xs text-slate-300">
                                <input type="checkbox" name="recipients[]" value="3" class="rounded text-emerald-500 bg-slate-800 border-slate-700">
                                <span>Phạm Quang Đăng (dang.pham@example.com)</span>
                            </label>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('admin.notifications.index') ? route('admin.notifications.index') : url('/admin/notifications') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Phát hành thông báo
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
