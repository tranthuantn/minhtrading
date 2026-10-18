@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>🔔 Quản Lý Thông Báo</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Gửi thông báo lịch học, nhận xét bài tập và tin tức thị trường quan trọng tới học viên</p>
        </div>

        <a href="{{ Route::has('admin.notifications.create') ? route('admin.notifications.create') : url('/admin/notifications/create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Tạo thông báo mới
        </a>
    </div>

    <!-- Notifications Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3.5 px-4">Tiêu đề thông báo</th>
                        <th class="py-3.5 px-4">Loại tin</th>
                        <th class="py-3.5 px-4">Gửi tới</th>
                        <th class="py-3.5 px-4">Ngày gửi</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($notifications ?? [] as $noti)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-white block">{{ $noti->title }}</span>
                                <span class="text-xs text-slate-400 line-clamp-1 mt-0.5">{{ $noti->message ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $notiTypeMap = [
                                        'info' => ['bg' => 'bg-sky-500/10 text-sky-400 border-sky-500/30', 'label' => 'Thông tin'],
                                        'warning' => ['bg' => 'bg-amber-500/10 text-amber-400 border-amber-500/30', 'label' => 'Cảnh báo'],
                                        'assignment' => ['bg' => 'bg-indigo-500/10 text-indigo-400 border-indigo-500/30', 'label' => 'Bài tập'],
                                        'urgent' => ['bg' => 'bg-rose-500/10 text-rose-400 border-rose-500/30', 'label' => 'Khẩn cấp'],
                                    ];
                                    $tInfo = $notiTypeMap[$noti->type ?? 'info'] ?? $notiTypeMap['info'];
                                @endphp
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $tInfo['bg'] }}">
                                    {{ $tInfo['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="px-2 py-1 rounded bg-slate-900 border border-slate-700 text-slate-300">
                                    {{ $noti->recipient_label ?? 'Tất cả học viên' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">
                                {{ isset($noti->created_at) ? $noti->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <form method="POST" action="{{ Route::has('admin.notifications.destroy') ? route('admin.notifications.destroy', $noti->id) : url('/admin/notifications/' . $noti->id) }}" onsubmit="return confirm('Bạn có chắc muốn xóa thông báo này?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-700/60" title="Xóa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-white block">Lịch Live Trading thực chiến tuần 43</span>
                                <span class="text-xs text-slate-400 line-clamp-1 mt-0.5">Thứ 3 và Thứ 5 lúc 19:30 trên Zoom. Phân tích trực tiếp các cặp tiền Forex và Vàng...</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border bg-sky-500/10 text-sky-400 border-sky-500/30">Thông tin</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="px-2 py-1 rounded bg-slate-900 border border-slate-700 text-slate-300">Tất cả học viên</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">18/03/2026 09:00</td>
                            <td class="py-3.5 px-4 text-right">
                                <button type="button" class="p-1.5 text-slate-400 hover:text-rose-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <span class="font-semibold text-white block">Cảnh báo tin tức lãi suất FED (FOMC) 01:00 đêm nay</span>
                                <span class="text-xs text-slate-400 line-clamp-1 mt-0.5">Khuyến nghị toàn bộ học viên quản lý vốn, không giữ lệnh qua đêm hoặc dời SL về BE...</span>
                            </td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium border bg-amber-500/10 text-amber-400 border-amber-500/30">Cảnh báo</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs">
                                <span class="px-2 py-1 rounded bg-slate-900 border border-slate-700 text-slate-300">Tất cả học viên</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">17/03/2026 16:30</td>
                            <td class="py-3.5 px-4 text-right">
                                <button type="button" class="p-1.5 text-slate-400 hover:text-rose-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($notifications) && method_exists($notifications, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
