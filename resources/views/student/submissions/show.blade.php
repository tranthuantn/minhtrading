@extends('layouts.student')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('student.submissions.index') ? route('student.submissions.index') : url('/student/submissions') }}" class="hover:text-white">Bài đã nộp</a>
                <span>/</span>
                <span class="text-sky-400">Chi tiết kết quả chấm</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>{{ $submission->assignment->title ?? 'Bài tập thực hành' }}</span>
                <x-status-badge :status="$submission->status ?? 'approved'" />
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Nộp ngày: {{ isset($submission->created_at) ? $submission->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
            </p>
        </div>

        <a href="{{ Route::has('student.submissions.index') ? route('student.submissions.index') : url('/student/submissions') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Review Feedback Alert / Card -->
    @if($submission->score !== null || !empty($submission->feedback))
        <div class="rounded-xl bg-gradient-to-br from-slate-800 to-slate-900 border border-emerald-500/40 p-6 shadow-xl space-y-4">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-400">Kết quả đánh giá từ Giảng viên MinhTrading</span>
                    <h3 class="text-lg font-bold text-white mt-1">Đã chấm điểm & hoàn thành nhận xét</h3>
                </div>
                <div class="text-right">
                    <span class="text-3xl font-extrabold font-mono text-emerald-400">{{ $submission->score ?? '9.0' }}</span>
                    <span class="text-sm text-slate-400">/ 10</span>
                </div>
            </div>

            <div class="p-4 rounded-lg bg-slate-950/70 border border-slate-800 text-sm text-slate-200 leading-relaxed italic">
                "{{ $submission->feedback ?? 'Rất tốt! Phân tích cấu trúc thị trường chuẩn xác, bắt đúng nhịp quét thanh khoản Liquidity Sweep. Điểm vào lệnh an toàn và tuân thủ chặt chẽ nguyên tắc tỷ lệ RR tối thiểu 1:2.' }}"
            </div>

            @if(!empty($submission->corrections))
                <div class="p-4 rounded-lg bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 space-y-1">
                    <div class="font-bold flex items-center gap-1.5 text-rose-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Lưu ý cần cải thiện:
                    </div>
                    <p class="leading-relaxed">{{ $submission->corrections }}</p>
                </div>
            @endif

            <!-- Resubmit Button if revision needed -->
            @if(($submission->status ?? '') === 'revision_needed')
                <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between">
                    <span class="text-xs text-rose-400 font-medium">Giảng viên yêu cầu nộp lại bài sau khi chỉnh sửa</span>
                    <a href="{{ Route::has('student.assignments.show') ? route('student.assignments.show', $submission->assignment_id) : url('/student/assignments/' . $submission->assignment_id) }}" 
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
            <span>Bài làm của bạn đang trong hàng đợi chờ giảng viên chấm điểm. Kết quả sẽ được cập nhật sớm nhất!</span>
        </div>
    @endif

    <!-- Submitted Content Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-4">
        <h3 class="text-sm font-bold text-white flex items-center gap-2">
            <span class="text-sky-400">📝</span>
            Nội dung bài làm bạn đã gửi
        </h3>
        <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-200 whitespace-pre-line leading-relaxed">
            {{ $submission->content ?? 'Em đã xác định vùng Demand H4 của cặp EURUSD sau một nhịp Liquidity Sweep tại đáy Á...' }}
        </div>
    </div>

    <!-- Trade Data Card (If exists) -->
    @php
        $tradeData = $submission->trade_data ?? [
            'pair' => 'EURUSD',
            'direction' => 'BUY',
            'entry_price' => '1.08450',
            'stop_loss' => '1.08200',
            'take_profit' => '1.09150',
            'risk_reward' => '1:2.8',
            'pnl_pips' => '+70 pips',
            'result' => 'win',
        ];
    @endphp
    @if(!empty($tradeData))
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
            <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                <span class="text-emerald-400">💹</span>
                Thông số lệnh đã gửi kèm
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Cặp tiền</span>
                    <span class="font-bold text-white text-sm">{{ $tradeData['pair'] ?? 'EURUSD' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Vị thế</span>
                    <span class="font-bold text-emerald-400 text-sm">{{ strtoupper($tradeData['direction'] ?? 'BUY') }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Giá Entry</span>
                    <span class="font-bold text-white text-sm">{{ $tradeData['entry_price'] ?? '1.08450' }}</span>
                </div>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                    <span class="text-slate-400 block text-[10px] uppercase font-sans">Risk : Reward</span>
                    <span class="font-bold text-sky-400 text-sm">{{ $tradeData['risk_reward'] ?? '1:2.8' }}</span>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
