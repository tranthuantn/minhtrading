@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" class="hover:text-white">Mô hình mẫu</a>
                <span>/</span>
                <span class="text-emerald-400">Chỉnh sửa</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Chỉnh Sửa Mô Hình Lệnh Mẫu</h1>
            <p class="text-sm text-slate-400">{{ $trade->title ?? 'Mô hình giao dịch mẫu' }}</p>
        </div>
        <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Form -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.model-trades.update') ? route('admin.model-trades.update', $trade->id ?? 1) : url('/admin/model-trades/' . ($trade->id ?? 1)) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tiêu đề mô hình mẫu <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $trade->title ?? '') }}" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('title') border-rose-500 @enderror"
                />
            </div>

            <!-- Parameters Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <!-- Currency Pair -->
                <div>
                    <label for="currency_pair" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Cặp tiền tệ <span class="text-rose-400">*</span>
                    </label>
                    <select 
                        name="currency_pair" 
                        id="currency_pair" 
                        required 
                        class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:border-emerald-500"
                    >
                        @foreach(['EURUSD', 'GBPUSD', 'USDJPY', 'XAUUSD', 'AUDUSD', 'USDCAD', 'USDCHF', 'NZDUSD', 'GBPJPY', 'EURJPY'] as $pair)
                            <option value="{{ $pair }}" {{ old('currency_pair', $trade->currency_pair ?? '') == $pair ? 'selected' : '' }}>{{ $pair }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Direction -->
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Vị thế <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-2 gap-2">
                        <label class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg border border-slate-700 bg-slate-900 cursor-pointer hover:border-emerald-500">
                            <input type="radio" name="direction" value="buy" {{ old('direction', $trade->direction ?? 'buy') == 'buy' ? 'checked' : '' }} class="text-emerald-500">
                            <span class="text-xs font-bold text-emerald-400 font-mono">BUY</span>
                        </label>
                        <label class="flex items-center justify-center gap-1.5 py-2 px-3 rounded-lg border border-slate-700 bg-slate-900 cursor-pointer hover:border-rose-500">
                            <input type="radio" name="direction" value="sell" {{ old('direction', $trade->direction ?? '') == 'sell' ? 'checked' : '' }} class="text-rose-500">
                            <span class="text-xs font-bold text-rose-400 font-mono">SELL</span>
                        </label>
                    </div>
                </div>

                <!-- Timeframe -->
                <div>
                    <label for="timeframe" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Khung thời gian <span class="text-rose-400">*</span>
                    </label>
                    <select 
                        name="timeframe" 
                        id="timeframe" 
                        required 
                        class="w-full px-3 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:border-emerald-500"
                    >
                        @foreach(['M1', 'M5', 'M15', 'M30', 'H1', 'H4', 'D1', 'W1'] as $tf)
                            <option value="{{ $tf }}" {{ old('timeframe', $trade->timeframe ?? 'H4') == $tf ? 'selected' : '' }}>{{ $tf }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Entry Price -->
                <div>
                    <label for="entry_price" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Giá vào lệnh (Entry) <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="entry_price" 
                        id="entry_price" 
                        value="{{ old('entry_price', $trade->entry_price ?? '') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-sm focus:outline-none focus:border-emerald-500"
                    />
                </div>

                <!-- Stop Loss -->
                <div>
                    <label for="stop_loss" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Cắt lỗ (Stop Loss) <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="stop_loss" 
                        id="stop_loss" 
                        value="{{ old('stop_loss', $trade->stop_loss ?? '') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-rose-400 font-mono text-sm focus:outline-none focus:border-emerald-500"
                    />
                </div>

                <!-- Take Profit -->
                <div>
                    <label for="take_profit" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Chốt lời (Take Profit) <span class="text-rose-400">*</span>
                    </label>
                    <input 
                        type="text" 
                        name="take_profit" 
                        id="take_profit" 
                        value="{{ old('take_profit', $trade->take_profit ?? '') }}" 
                        required 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-emerald-400 font-mono text-sm focus:outline-none focus:border-emerald-500"
                    />
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tóm tắt mô hình
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="2" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500"
                >{{ old('description', $trade->description ?? '') }}</textarea>
            </div>

            <!-- Analysis Notes -->
            <div>
                <label for="analysis" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Phân tích chi tiết từng bước <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="analysis" 
                    id="analysis" 
                    rows="6" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm font-sans focus:outline-none focus:border-emerald-500"
                >{{ old('analysis', $trade->analysis ?? '') }}</textarea>
            </div>

            <!-- Setup Criteria -->
            <div>
                <label for="setup_criteria" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Checklist tiêu chí thỏa mãn setup
                </label>
                <textarea 
                    name="setup_criteria" 
                    id="setup_criteria" 
                    rows="3" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm font-mono focus:outline-none focus:border-emerald-500"
                >{{ old('setup_criteria', $trade->setup_criteria ?? '') }}</textarea>
            </div>

            <!-- Chart Screenshots Upload -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Ảnh chụp biểu đồ phân tích (Tải ảnh mới nếu muốn thay)
                </label>
                <input 
                    type="file" 
                    name="chart_image" 
                    accept="image/*" 
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600 cursor-pointer"
                />
            </div>

            <!-- Published Toggle -->
            <div x-data="{ published: {{ old('is_published', $trade->is_published ?? 1) ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between p-4 rounded-lg bg-slate-900 border border-slate-700">
                    <div>
                        <span class="text-sm font-semibold text-white">Xuất bản cho học viên</span>
                        <p class="text-xs text-slate-400">Hiển thị mô hình này trong thư viện Case Study</p>
                    </div>
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

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Cập nhật mô hình
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
