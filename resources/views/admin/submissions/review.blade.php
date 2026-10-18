@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" class="hover:text-white">Bài nộp</a>
                <span>/</span>
                <span class="text-emerald-400">Chấm điểm & Phản hồi</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Chấm Điểm Bài Nộp</h1>
            <p class="text-sm text-slate-400">
                Bài tập: <span class="text-white font-medium">{{ $submission->assignment->title ?? 'Thực hành Backtest' }}</span> • Học viên: <span class="text-emerald-400 font-semibold">{{ $submission->user->name ?? 'Học viên' }}</span>
            </p>
        </div>

        <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Review Grid: Left = Submission Content, Right = Review Form -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Left: Submission Content (7 cols) -->
        <div class="lg:col-span-7 space-y-6">
            <!-- Student Content Card -->
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                <div class="flex items-center justify-between pb-3 border-b border-slate-700/60 mb-3">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>📝 Bài làm của học viên</span>
                    </h3>
                    <span class="text-xs text-slate-400 font-mono">{{ isset($submission->created_at) ? $submission->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}</span>
                </div>

                <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-200 leading-relaxed whitespace-pre-line">
                    {{ $submission->content ?? 'Em đã backtest theo phương pháp SMC: Nhận diện phiên Á tích lũy (Asian Range), sau đó phiên London quét qua đáy Á (Liquidity Sweep) và tạo một nến CHoCH (Change of Character) trên khung M15. Điểm vào lệnh tại vùng FVG với Risk:Reward là 1:3.' }}
                </div>
            </div>

            <!-- Trade Parameters (If backtest/trade submission) -->
            @php
                $tradeData = $submission->trade_data ?? [
                    'pair' => 'EURUSD',
                    'direction' => 'BUY',
                    'entry_price' => '1.08450',
                    'stop_loss' => '1.08200',
                    'take_profit' => '1.09150',
                    'risk_reward' => '1:3.0',
                    'pnl_pips' => '+70 pips',
                    'result' => 'win',
                ];
            @endphp
            @if(!empty($tradeData))
                <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                    <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-emerald-400">💹</span>
                        Thông số lệnh backtest
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Cặp tiền</span>
                            <span class="font-bold text-white">{{ $tradeData['pair'] ?? 'EURUSD' }}</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Vị thế</span>
                            <span class="font-bold {{ strtolower($tradeData['direction'] ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">{{ strtoupper($tradeData['direction'] ?? 'BUY') }}</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Entry</span>
                            <span class="font-bold text-white">{{ $tradeData['entry_price'] ?? '1.08450' }}</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">R:R</span>
                            <span class="font-bold text-sky-400">{{ $tradeData['risk_reward'] ?? '1:3.0' }}</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Stop Loss</span>
                            <span class="font-bold text-rose-400">{{ $tradeData['stop_loss'] ?? '1.08200' }}</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Take Profit</span>
                            <span class="font-bold text-emerald-400">{{ $tradeData['take_profit'] ?? '1.09150' }}</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Kết quả</span>
                            <span class="font-bold text-emerald-400">WIN</span>
                        </div>
                        <div class="p-2.5 rounded bg-slate-900 border border-slate-800">
                            <span class="text-[10px] text-slate-400 block font-sans">Pips</span>
                            <span class="font-bold text-emerald-400">{{ $tradeData['pnl_pips'] ?? '+70' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Chart Preview -->
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <span class="text-amber-400">🖼️</span>
                    Biểu đồ đính kèm của học viên
                </h3>
                <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-950 p-4 text-center">
                    <div class="w-full h-48 flex items-center justify-center bg-slate-900/60 rounded border border-slate-800 text-slate-400 text-xs">
                        <div class="text-center">
                            <svg class="w-10 h-10 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <span class="font-mono text-emerald-400">EURUSD_H4_Setup_Analysis.png</span>
                            <p class="text-[11px] text-slate-500 mt-0.5">Nhấp để mở xem kích thước đầy đủ</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Review Form (5 cols) -->
        <div class="lg:col-span-5">
            <div class="sticky top-20 rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-6">
                <div class="pb-3 border-b border-slate-700/60">
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                        Bảng Đánh Giá & Nhận Xét
                    </h3>
                    <p class="text-xs text-slate-400 mt-1">Kết quả chấm điểm sẽ được thông báo ngay cho học viên</p>
                </div>

                <form method="POST" action="{{ route('admin.submissions.storeReview', $submission->id) }}" class="space-y-5">
                    @csrf

                    <!-- Score (0-10) -->
                    <div>
                        <label for="score" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Điểm số (Thang điểm 10) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <input 
                                type="number" 
                                name="score" 
                                id="score" 
                                step="0.1" 
                                min="0" 
                                max="10" 
                                value="{{ old('score', $submission->score ?? 9.0) }}" 
                                required 
                                placeholder="8.5" 
                                class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-lg font-bold focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('score') border-rose-500 @enderror"
                            />
                            <span class="absolute right-4 top-3 text-xs text-slate-500 font-bold">/ 10.0</span>
                        </div>
                        @error('score')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status Selection -->
                    <div>
                        <label for="status" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Kết luận trạng thái <span class="text-rose-400">*</span>
                        </label>
                        <select 
                            name="status" 
                            id="status" 
                            required 
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500"
                        >
                            <option value="approved" {{ old('status', $submission->status ?? '') == 'approved' ? 'selected' : '' }}>
                                ✅ Đạt (Approved) - Bài làm đạt chuẩn
                            </option>
                            <option value="reviewed" {{ old('status', $submission->status ?? '') == 'reviewed' ? 'selected' : '' }}>
                                ℹ️ Đã chấm (Reviewed) - Đã ghi nhận điểm
                            </option>
                            <option value="revision_needed" {{ old('status', $submission->status ?? '') == 'revision_needed' ? 'selected' : '' }}>
                                ⚠️ Cần sửa lại (Revision Needed) - Yêu cầu làm lại
                            </option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Đánh giá bài tập tiêu biểu -->
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-700/80 space-y-3" x-data="{ isFeatured: {{ old('is_featured', $submission->is_featured ? 'true' : 'false') }} }">
                        <label class="flex items-start gap-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="is_featured" 
                                   value="1" 
                                   x-model="isFeatured"
                                   class="mt-0.5 rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-amber-500 w-4 h-4">
                            <div>
                                <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                                    ⭐ Đánh giá là BÀI TẬP TIÊU BIỂU
                                </span>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Bài làm này sẽ được vinh danh trong Showcase để toàn bộ học viên cùng tham khảo học hỏi.
                                </p>
                            </div>
                        </label>

                        <div x-show="isFeatured" x-collapse class="pt-2 border-t border-slate-800">
                            <label class="block text-xs font-semibold text-slate-300 mb-1">Ghi chú lý do tiêu biểu (hiển thị công khai)</label>
                            <textarea name="featured_note" 
                                      rows="2" 
                                      placeholder="Ví dụ: Phân tích SMC chuẩn chỉ, quản lý vốn chặt chẽ và ghi chép nhật ký lệnh rất mẫu mực." 
                                      class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-amber-500">{{ old('featured_note', $submission->featured_note) }}</textarea>
                        </div>
                    </div>

                    <!-- Feedback Textarea -->
                    <div>
                        <label for="feedback" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Lời khen & Nhận xét của giảng viên <span class="text-rose-400">*</span>
                        </label>
                        <textarea 
                            name="feedback" 
                            id="feedback" 
                            rows="4" 
                            required 
                            placeholder="Nhận xét về điểm mạnh, tư duy phân tích và kỷ luật của học viên..." 
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('feedback') border-rose-500 @enderror"
                        >{{ old('feedback', $submission->feedback ?? 'Phân tích rất tốt! Nhận diện đúng cấu trúc và tuân thủ chặt chẽ quản lý vốn.') }}</textarea>
                        @error('feedback')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Corrections Textarea -->
                    <div>
                        <label for="corrections" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                            Gợi ý chỉnh sửa & Lưu ý rủi ro (Nội dung cần sửa)
                        </label>
                        <textarea 
                            name="corrections" 
                            id="corrections" 
                            rows="3" 
                            placeholder="Lưu ý về việc dời SL khi giá đạt 1R, tránh bẫy tin tức Nonfarm..." 
                            class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('corrections') border-rose-500 @enderror"
                        >{{ old('corrections', $submission->corrections ?? '') }}</textarea>
                        @error('corrections')
                            <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4 border-t border-slate-700/60">
                        <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/25 transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Lưu Đánh Giá & Gửi Cho Học Viên
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
