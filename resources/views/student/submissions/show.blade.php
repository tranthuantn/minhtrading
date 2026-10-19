@extends('layouts.student')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                @if(!empty($isFeatured) && empty($isOwner))
                    <a href="{{ route('student.featured.index') }}" class="hover:text-amber-400 text-amber-300 flex items-center gap-1">
                        <span>⭐</span> Góc Tiêu Biểu
                    </a>
                    <span>/</span>
                    <span class="text-white">Chi tiết bài tập tiêu biểu</span>
                @else
                    <a href="{{ route('student.assignments.index', ['status' => 'submitted']) }}" class="hover:text-white">Bài đã nộp</a>
                    <span>/</span>
                    <span class="text-sky-400">Chi tiết kết quả chấm</span>
                @endif
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>{{ $submission->assignment->title ?? 'Bài tập thực hành' }}</span>
                <x-status-badge :status="$submission->status ?? 'approved'" />
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Nộp bởi: <span class="text-white font-medium">{{ $submission->student->name ?? 'Học viên' }}</span>
                • Thời gian: {{ isset($submission->created_at) ? $submission->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
            </p>
        </div>

        <a href="{{ (!empty($isFeatured) && empty($isOwner)) ? route('student.featured.index') : route('student.assignments.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Featured Banner if marked as exemplary -->
    @if($submission->is_featured)
        <div class="rounded-xl bg-gradient-to-r from-amber-500/20 via-amber-600/10 to-transparent border border-amber-500/40 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 shadow-lg shadow-amber-500/5">
            <div class="flex items-center gap-3">
                <span class="text-2xl">⭐</span>
                <div>
                    <h3 class="text-sm font-bold text-amber-300">Bài Tập Tiêu Biểu Xuất Sắc (Chia sẻ toàn hệ thống)</h3>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Tác giả: <span class="font-bold text-white">{{ $submission->student->name ?? 'Học viên' }}</span>
                        • Khóa: <span class="text-emerald-400 font-semibold">{{ $submission->assignment->lesson->course->title ?? 'Khóa học' }}</span>
                        • Bài: {{ $submission->assignment->lesson->title ?? 'Bài học' }}
                    </p>
                </div>
            </div>
            @if($submission->featured_at)
                <span class="text-[11px] text-amber-400 font-mono shrink-0">Vinh danh: {{ $submission->featured_at->format('d/m/Y') }}</span>
            @endif
        </div>
    @endif

    <!-- Instructor's Featured Note -->
    @if($submission->featured_note)
        <div class="p-4 rounded-xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5 text-amber-300 text-xs">
                <span>💡 Lý do vinh danh từ Giảng viên:</span>
            </div>
            <p class="text-sm leading-relaxed italic">"{{ $submission->featured_note }}"</p>
        </div>
    @endif

    <!-- Review Feedback Alert / Card -->
    @php
        $latestReview = $submission->latestReview ?? $submission->reviews->first();
        $score = $submission->score ?? $latestReview?->score;
        $feedback = $submission->feedback ?? $latestReview?->feedback;
        $corrections = $submission->corrections ?? $latestReview?->corrections;
    @endphp

    @if($score !== null || !empty($feedback))
        <div class="rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 border border-emerald-500/40 p-6 shadow-xl space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Kết quả đánh giá từ Giảng viên</span>
                    <h3 class="text-lg font-bold text-white mt-1">Đã chấm điểm & hoàn thành nhận xét</h3>
                </div>
                @if($score !== null)
                    <div class="text-right">
                        <span class="text-3xl font-extrabold font-mono text-emerald-400">{{ $score }}</span>
                        <span class="text-sm text-slate-400">/ 10</span>
                    </div>
                @endif
            </div>

            @if(!empty($feedback))
                <div class="p-4 rounded-lg bg-slate-950/70 border border-slate-800 text-sm text-slate-200 leading-relaxed italic">
                    "{{ $feedback }}"
                </div>
            @endif

            @if(!empty($corrections))
                <div class="p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-rose-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Lưu ý cần cải thiện:
                    </div>
                    <p class="leading-relaxed">{{ $corrections }}</p>
                </div>
            @endif

            <!-- Resubmit Button if revision needed and current user is owner -->
            @if(!empty($isOwner) && ($submission->status ?? '') === 'revision_needed')
                <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between">
                    <span class="text-xs text-rose-400 font-medium">Giảng viên yêu cầu nộp lại bài sau khi chỉnh sửa</span>
                    <a href="{{ route('student.assignments.show', $submission->assignment_id) }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-rose-600 hover:bg-rose-500 text-white shadow transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Sửa & Nộp lại bài
                    </a>
                </div>
            @endif
        </div>
    @else
        <div class="p-4 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs flex items-center gap-3">
            <svg class="w-5 h-5 shrink-0 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Bài làm đang trong hàng đợi chờ giảng viên chấm điểm. Kết quả sẽ được cập nhật sớm nhất!</span>
        </div>
    @endif

    <!-- Submitted Content Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
            <span class="text-sky-400">📝</span>
            Nội dung bài làm
        </h3>
        <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-200 whitespace-pre-line leading-relaxed">
            {{ $submission->content }}
        </div>
    </div>

    <!-- Screenshots and Attachments Gallery -->
    @php
        $attachments = is_array($submission->attachments) ? $submission->attachments : [];
        if (empty($attachments) && !empty($submission->attachment_path)) {
            $attachments = [['url' => $submission->attachment_path, 'name' => 'File đính kèm']];
        }
    @endphp

    @if(!empty($attachments) || !empty($submission->chart_url))
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-amber-400">🖼️</span>
                Hình ảnh biểu đồ & Tài liệu đính kèm
            </h3>

            @if(!empty($submission->chart_url))
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Link biểu đồ TradingView:</span>
                    <a href="{{ $submission->chart_url }}" target="_blank" class="text-sky-400 hover:underline flex items-center gap-1 font-mono">
                        {{ $submission->chart_url }}
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            @endif

            @if(!empty($attachments))
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    @foreach($attachments as $att)
                        @php
                            $attUrl = is_array($att) ? ($att['url'] ?? '') : $att;
                            $attName = is_array($att) ? ($att['name'] ?? 'File đính kèm') : 'File đính kèm';
                            $url = str_starts_with($attUrl, 'http') ? $attUrl : asset('storage/' . $attUrl);
                            $isImg = preg_match('/\.(jpg|jpeg|png|webp|gif)$/i', $attUrl);
                        @endphp

                        @if($isImg)
                            <a href="{{ $url }}" target="_blank" class="group block rounded-xl overflow-hidden border border-slate-700 hover:border-amber-400 transition bg-slate-900">
                                <img src="{{ $url }}" alt="Attachment" class="w-full h-44 object-cover group-hover:scale-105 transition-transform">
                            </a>
                        @else
                            <a href="{{ $url }}" target="_blank" download class="p-4 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 flex items-center gap-3 text-xs text-white transition">
                                <svg class="w-5 h-5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="truncate">{{ $attName }}</span>
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
    @endif

    <!-- Trade Data Card (If exists) -->
    @if(!empty($submission->trade_data))
        @php
            $tradeData = $submission->trade_data;
        @endphp
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <span class="text-emerald-400">💹</span>
                Thông số lệnh đã gửi kèm
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Cặp tiền</span>
                    <span class="font-bold text-white text-sm">{{ $tradeData['pair'] ?? 'N/A' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Vị thế</span>
                    <span class="font-bold text-emerald-400 text-sm">{{ strtoupper($tradeData['direction'] ?? 'BUY') }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Giá Entry</span>
                    <span class="font-bold text-white text-sm">{{ $tradeData['entry_price'] ?? 'N/A' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Risk : Reward</span>
                    <span class="font-bold text-sky-400 text-sm">{{ $tradeData['risk_reward'] ?? '1:2' }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
