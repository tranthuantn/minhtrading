@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" class="hover:text-white">Bài tập</a>
                <span>/</span>
                <span class="text-emerald-400">Tạo mới</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Tạo Bài Tập Mới</h1>
            <p class="text-sm text-slate-400">Giao bài thực hành phân tích thị trường, backtest hoặc nhật ký lệnh cho học viên</p>
        </div>
        <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.assignments.store') ? route('admin.assignments.store') : url('/admin/assignments') }}" class="space-y-6">
            @csrf

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tiêu đề bài tập <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title') }}" 
                    required 
                    placeholder="Ví dụ: Thực hành Backtest 15 lệnh mô hình Double Bottom trên cặp EURUSD" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('title') border-rose-500 @enderror"
                />
                @error('title')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Lesson Selection (grouped by Course) -->
                <div>
                    <label for="lesson_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Thuộc bài học <span class="text-rose-400">*</span>
                    </label>
                    <select 
                        name="lesson_id" 
                        id="lesson_id" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                    >
                        <option value="">-- Chọn bài học --</option>
                        @if(isset($courses))
                            @foreach($courses as $c)
                                <optgroup label="Khóa: {{ $c->title }}">
                                    @if(isset($c->lessons))
                                        @foreach($c->lessons as $l)
                                            <option value="{{ $l->id }}" {{ old('lesson_id') == $l->id ? 'selected' : '' }}>
                                                {{ $l->title }}
                                            </option>
                                        @endforeach
                                    @endif
                                </optgroup>
                            @endforeach
                        @else
                            <optgroup label="Khóa: Price Action & Cấu Trúc Thị Trường">
                                <option value="1">Bài 1: Cấu trúc cơ bản nến Nhật</option>
                                <option value="2">Bài 2: Vùng Supply & Demand</option>
                                <option value="3">Bài 3: Nhận biết bẫy thanh khoản Liquidity</option>
                            </optgroup>
                            <optgroup label="Khóa: Quản Trị Vốn & Tâm Lý Giao Dịch">
                                <option value="4">Bài 1: Kế hoạch phân bổ vốn</option>
                                <option value="5">Bài 2: Tỷ lệ Risk:Reward tối ưu</option>
                            </optgroup>
                        @endif
                    </select>
                    @error('lesson_id')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Assignment Type -->
                <div>
                    <label for="type" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Hình thức nộp bài <span class="text-rose-400">*</span>
                    </label>
                    <select 
                        name="type" 
                        id="type" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                    >
                        <option value="backtest" {{ old('type') == 'backtest' ? 'selected' : '' }}>Nhật ký Backtest (Điền thông số lệnh)</option>
                        <option value="trade_entry" {{ old('type') == 'trade_entry' ? 'selected' : '' }}>Lệnh thực tế Demo/Live</option>
                        <option value="chart_analysis" {{ old('type') == 'chart_analysis' ? 'selected' : '' }}>Phân tích ảnh Chart vẽ TradingView</option>
                        <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>Tự luận / Trả lời lý thuyết</option>
                    </select>
                    @error('type')
                        <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Due Date -->
            <div>
                <label for="due_date" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Hạn chót nộp bài (Deadline)
                </label>
                <input 
                    type="datetime-local" 
                    name="due_date" 
                    id="due_date" 
                    value="{{ old('due_date') }}" 
                    class="w-full sm:w-80 px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                />
                <p class="mt-1 text-xs text-slate-500">Để trống nếu bài tập không giới hạn thời gian nộp</p>
                @error('due_date')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Mô tả bài tập <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="4" 
                    required 
                    placeholder="Giải thích mục đích bài tập, bối cảnh thực hành và lưu ý..." 
                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('description') border-rose-500 @enderror"
                >{{ old('description') }}</textarea>
                @error('description')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Requirements / Criteria -->
            <div>
                <label for="requirements" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Yêu cầu chi tiết & Tiêu chí chấm điểm
                </label>
                <textarea 
                    name="requirements" 
                    id="requirements" 
                    rows="4" 
                    placeholder="1. Chụp ảnh chart rõ ràng khung H4 và M15&#10;2. Đánh dấu rõ vùng SL và TP với RR tối thiểu 1:2&#10;3. Nêu lý do vào lệnh..." 
                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm font-mono @error('requirements') border-rose-500 @enderror"
                >{{ old('requirements') }}</textarea>
                @error('requirements')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Giao bài tập
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
