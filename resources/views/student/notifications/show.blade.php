@extends('layouts.student')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('student.notifications.index') ? route('student.notifications.index') : url('/student/notifications') }}" class="hover:text-white">Thông báo</a>
                <span>/</span>
                <span class="text-sky-400">Chi tiết thông báo</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $notification->title ?? 'Lịch Live Trading tuần này trên Zoom' }}</h1>
        </div>

        <a href="{{ Route::has('student.notifications.index') ? route('student.notifications.index') : url('/student/notifications') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại
        </a>
    </div>

    <!-- Notification Content Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 sm:p-8 shadow-xl space-y-5">
        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60 flex-wrap gap-2">
            <div class="flex items-center gap-2">
                @php
                    $typeMap = [
                        'info' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'label' => 'Thông tin chung'],
                        'warning' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'label' => 'Cảnh báo rủi ro'],
                        'assignment' => ['bg' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30', 'label' => 'Bài tập'],
                        'urgent' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'label' => 'Khẩn cấp'],
                    ];
                    $tInfo = $typeMap[$notification->type ?? 'info'] ?? $typeMap['info'];
                @endphp
                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $tInfo['bg'] }}">
                    {{ $tInfo['label'] }}
                </span>
                <span class="text-xs text-slate-400">Từ: <strong>Giảng viên MinhTrading</strong></span>
            </div>

            <span class="text-xs text-slate-400 font-mono">
                {{ isset($notification->created_at) ? $notification->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
            </span>
        </div>

        <!-- Body Message -->
        <div class="text-slate-200 text-sm leading-relaxed whitespace-pre-line font-sans">
            {{ $notification->message ?? "Thân gửi các bạn học viên MinhTrading,\n\nTuần này chúng ta sẽ có 2 buổi Live Trading thực chiến phân tích trực tiếp trên thị trường lúc 19:30 tối Thứ 3 và Thứ 5.\n\nNội dung chính:\n1. Nhận định xu hướng Vàng (XAUUSD) và EURUSD trước thềm tin tức CPI.\n2. Thực hành đọc nến M15 và tìm điểm vào lệnh với SL siêu ngắn.\n3. Giải đáp thắc mắc và sửa bài tập trực tiếp cho từng học viên.\n\nThông tin phòng Zoom:\n- Link: https://zoom.us/j/8889991234\n- Passcode: MINHTRADING2026\n\nChúc anh em giao dịch an toàn và tuân thủ kỷ luật vốn!" }}
        </div>

        <div class="pt-6 border-t border-slate-700/60 flex items-center justify-between">
            <span class="text-xs text-slate-500">MinhTrading Forex Academy • Đồng hành cùng Trader</span>
            <a href="{{ Route::has('student.dashboard') ? route('student.dashboard') : url('/student/dashboard') }}" 
               class="text-xs font-semibold text-sky-400 hover:text-sky-300">
                Về trang chủ &rarr;
            </a>
        </div>
    </div>
</div>
@endsection
