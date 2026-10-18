@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" class="hover:text-white">Mô hình mẫu</a>
                <span>/</span>
                <span class="text-amber-400">Trích xuất từ lệnh học viên</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Chuyển Lệnh Của Học Viên Thành Mô Hình Mẫu</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Học viên: {{ $trade->user->name ?? 'Vũ Đức Nam' }}</span>
            </h1>
            <p class="text-sm text-slate-400">Dùng lệnh thực tế xuất sắc của học viên để tạo bài giảng thực chiến cho toàn bộ lớp học</p>
        </div>
        <a href="{{ url()->previous() ?? (Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades')) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Original Trade Info Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
        <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
            <span class="text-sky-400">ℹ️</span>
            Dữ liệu lệnh gốc của học viên
        </h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
            <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                <span class="text-[10px] text-slate-400 block font-sans">Cặp tiền</span>
                <span class="font-bold text-white text-sm">{{ $trade->currency_pair ?? 'EURUSD' }}</span>
            </div>
            <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                <span class="text-[10px] text-slate-400 block font-sans">Vị thế</span>
                <span class="font-bold text-sm {{ strtolower($trade->direction ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                    {{ strtoupper($trade->direction ?? 'BUY') }} ({{ $trade->timeframe ?? 'H4' }})
                </span>
            </div>
            <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                <span class="text-[10px] text-slate-400 block font-sans">Giá Entry</span>
                <span class="font-bold text-white text-sm">{{ $trade->entry_price ?? '1.08450' }}</span>
            </div>
            <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                <span class="text-[10px] text-slate-400 block font-sans">Lợi nhuận P&L</span>
                <span class="font-bold text-emerald-400 text-sm">+{{ $trade->pnl_pips ?? 75 }} pips</span>
            </div>
        </div>

        @if(!empty($trade->analysis_notes))
            <div class="mt-3 pt-3 border-t border-slate-700/60 text-xs text-slate-300">
                <span class="text-slate-500 font-sans font-medium block mb-1">Ghi chú gốc của học viên:</span>
                <p class="italic bg-slate-900/50 p-2.5 rounded border border-slate-800">{{ $trade->analysis_notes }}</p>
            </div>
        @endif
    </div>

    <!-- Admin Conversion Form -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form method="POST" action="{{ Route::has('admin.model-trades.store') ? route('admin.model-trades.store') : url('/admin/model-trades') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Hidden Inputs for Source -->
            <input type="hidden" name="source_trade_id" value="{{ $trade->id ?? 1 }}">
            <input type="hidden" name="currency_pair" value="{{ $trade->currency_pair ?? 'EURUSD' }}">
            <input type="hidden" name="direction" value="{{ $trade->direction ?? 'buy' }}">
            <input type="hidden" name="timeframe" value="{{ $trade->timeframe ?? 'H4' }}">
            <input type="hidden" name="entry_price" value="{{ $trade->entry_price ?? '1.08450' }}">
            <input type="hidden" name="stop_loss" value="{{ $trade->stop_loss ?? '1.08200' }}">
            <input type="hidden" name="take_profit" value="{{ $trade->take_profit ?? '1.09200' }}">

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Đặt tiêu đề chuẩn cho mô hình mẫu <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', 'Setup mẫu: ' . ($trade->currency_pair ?? 'EURUSD') . ' ' . strtoupper($trade->direction ?? 'BUY') . ' chuẩn SMC') }}" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 text-sm"
                />
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tóm tắt mô hình & Lời khen cho học viên
                </label>
                <textarea 
                    name="description" 
                    id="description" 
                    rows="2" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500"
                >{{ old('description', 'Lệnh vào lệnh mẫu cực kỳ đẹp của học viên ' . ($trade->user->name ?? 'Nam') . ' bắt trọn sóng tăng sau nhịp Spring quét thanh khoản.') }}</textarea>
            </div>

            <!-- Analysis Notes -->
            <div>
                <label for="analysis" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Phân tích chuyên sâu từ Giảng viên (Bài học rút ra) <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="analysis" 
                    id="analysis" 
                    rows="6" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500"
                >{{ old('analysis', "1. Phân tích bối cảnh: Xu hướng tăng trên D1 vẫn giữ vững.\n2. Vùng kích hoạt lệnh: Giá chạm Order Block H4 và tạo cụm nến xác nhận trên M15.\n3. Quản trị rủi ro: Stop loss rất chặt (25 pips), Take profit 75 pips đạt tỷ lệ chuẩn 1:3.\n4. Bài học: Kiên nhẫn chờ giá về đúng vùng cản mạnh thay vì đu đỉnh fomo.") }}</textarea>
            </div>

            <!-- Setup Criteria -->
            <div>
                <label for="setup_criteria" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Checklist tiêu chí
                </label>
                <textarea 
                    name="setup_criteria" 
                    id="setup_criteria" 
                    rows="3" 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-sm font-mono focus:outline-none focus:border-emerald-500"
                >{{ old('setup_criteria', "- Xu hướng HTF: Tăng rõ ràng\n- Quét thanh khoản Liquidity Sweep: Có\n- Điểm vào lệnh tại FVG / OB: Chuẩn xác\n- RR: Đạt 1:3") }}</textarea>
            </div>

            <!-- Chart Image -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Hình ảnh biểu đồ (Giữ ảnh gốc của học viên hoặc tải ảnh chú thích mới)
                </label>
                <input 
                    type="file" 
                    name="chart_image" 
                    accept="image/*" 
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600 cursor-pointer"
                />
            </div>

            <!-- Published Toggle -->
            <div x-data="{ published: true }">
                <div class="flex items-center justify-between p-4 rounded-lg bg-slate-900 border border-slate-700">
                    <div>
                        <span class="text-sm font-semibold text-white">Xuất bản ngay cho học viên</span>
                        <p class="text-xs text-slate-400">Hiển thị trong danh sách mô hình mẫu để học viên học tập</p>
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
                    Tạo & Xuất Bản Mô Hình Mẫu
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
