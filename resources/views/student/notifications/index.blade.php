@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>🔔 Thông Báo Của Bạn</span>
                @if(($unreadNotificationCount ?? 0) > 0)
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                        {{ $unreadNotificationCount }} chưa đọc
                    </span>
                @endif
            </h1>
            <p class="text-sm text-slate-400 mt-1">Thông báo lịch học, nhận xét bài tập từ giảng viên và cập nhật thị trường</p>
        </div>

        <form method="POST" action="{{ Route::has('student.notifications.mark-all-read') ? route('student.notifications.mark-all-read') : url('/student/notifications/mark-all-read') }}">
            @csrf
            <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-sky-400 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                Đánh dấu tất cả đã đọc
            </button>
        </form>
    </div>

    <!-- Notification List -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="divide-y divide-slate-700/60">
            @forelse($notifications ?? [] as $notification)
                @php
                    $isUnread = empty($notification->read_at);
                @endphp
                <a href="{{ Route::has('student.notifications.show') ? route('student.notifications.show', $notification->id) : url('/student/notifications/' . $notification->id) }}" 
                   class="p-4 sm:p-5 flex items-start gap-4 hover:bg-slate-750 transition-colors block group {{ $isUnread ? 'bg-sky-500/5' : '' }}">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 {{ $isUnread ? 'bg-sky-500/20 text-sky-400 border border-sky-500/30' : 'bg-slate-700/60 text-slate-400' }}">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <h3 class="text-sm font-semibold {{ $isUnread ? 'text-white font-bold' : 'text-slate-300' }} group-hover:text-sky-400 transition-colors">
                                    {{ $notification->title }}
                                </h3>
                                @if($isUnread)
                                    <span class="w-2 h-2 rounded-full bg-sky-400 ring-2 ring-slate-900"></span>
                                @endif
                            </div>
                            <span class="text-xs text-slate-400 font-mono shrink-0">
                                {{ isset($notification->created_at) ? $notification->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                            {{ $notification->message }}
                        </p>
                    </div>

                    <svg class="w-4 h-4 text-slate-500 group-hover:text-sky-400 group-hover:translate-x-1 transition-all self-center shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            @empty
                <a href="{{ Route::has('student.notifications.show') ? route('student.notifications.show', 1) : url('/student/notifications/1') }}" 
                   class="p-4 sm:p-5 flex items-start gap-4 hover:bg-slate-750 transition-colors bg-sky-500/5 group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 bg-sky-500/20 text-sky-400 border border-sky-500/30">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-white group-hover:text-sky-400">Lịch Live Trading tuần này trên Zoom</h3>
                                <span class="w-2 h-2 rounded-full bg-sky-400 ring-2 ring-slate-900"></span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono shrink-0">18/03/2026 09:00</span>
                        </div>
                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                            Buổi phân tích trực tiếp thị trường phiên Âu và Mỹ cùng Thầy Minh lúc 19:30 tối nay. Link Zoom và passcode đã được đính kèm bên trong...
                        </p>
                    </div>
                </a>

                <a href="{{ Route::has('student.notifications.show') ? route('student.notifications.show', 2) : url('/student/notifications/2') }}" 
                   class="p-4 sm:p-5 flex items-start gap-4 hover:bg-slate-750 transition-colors group">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5 bg-slate-700/60 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2 mb-1">
                            <h3 class="text-sm font-semibold text-slate-300 group-hover:text-sky-400">Giảng viên đã chấm bài tập Backtest của bạn</h3>
                            <span class="text-xs text-slate-400 font-mono shrink-0">16/03/2026 15:40</span>
                        </div>
                        <p class="text-xs text-slate-400 line-clamp-2 leading-relaxed">
                            Bạn đã đạt 9.0/10 điểm cho bài tập Backtest mô hình Pin Bar. Xem chi tiết nhận xét và góp ý từ giảng viên...
                        </p>
                    </div>
                </a>
            @endforelse
        </div>

        @if(isset($notifications) && method_exists($notifications, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
