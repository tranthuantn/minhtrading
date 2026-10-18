@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" class="hover:text-white">Bài tập</a>
                <span>/</span>
                <span class="text-emerald-400">Chi tiết & Danh sách nộp bài</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $assignment->title ?? 'Chi tiết bài tập thực hành' }}</h1>
            <p class="text-sm text-slate-400">
                Khóa: {{ $assignment->lesson->course->title ?? 'Price Action Thực Chiến' }} • Bài: {{ $assignment->lesson->title ?? 'Bài 2' }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.assignments.edit') ? route('admin.assignments.edit', $assignment->id ?? 1) : url('/admin/assignments/' . ($assignment->id ?? 1) . '/edit') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa bài tập
            </a>
            <a href="{{ Route::has('admin.assignments.index') ? route('admin.assignments.index') : url('/admin/assignments') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Assignment Overview Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-4">
        <div>
            <h4 class="text-xs font-semibold uppercase text-slate-400 tracking-wider mb-1">Mô tả bài tập</h4>
            <p class="text-sm text-slate-200 leading-relaxed">{{ $assignment->description ?? 'Thực hiện backtest 20 lệnh trên biểu đồ EURUSD khung H4 áp dụng đúng mẫu nến Pin Bar tại Supply Demand. Ghi lại tỷ lệ Risk Reward và P&L pips đạt được.' }}</p>
        </div>

        @if(!empty($assignment->requirements))
            <div class="pt-3 border-t border-slate-700/60">
                <h4 class="text-xs font-semibold uppercase text-slate-400 tracking-wider mb-1">Yêu cầu & Tiêu chí chấm điểm</h4>
                <div class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 font-mono whitespace-pre-line leading-relaxed">{{ $assignment->requirements }}</div>
            </div>
        @endif

        <div class="pt-3 border-t border-slate-700/60 flex items-center gap-6 text-xs text-slate-400 flex-wrap">
            <div>Loại bài: <span class="text-sky-400 font-semibold">{{ ucfirst($assignment->type ?? 'Backtest') }}</span></div>
            <div>Hạn nộp: <span class="text-amber-400 font-mono font-medium">{{ isset($assignment->due_date) ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : 'Không giới hạn' }}</span></div>
            <div>Tổng bài đã nộp: <span class="text-white font-mono font-bold">{{ isset($assignment->submissions) ? count($assignment->submissions) : 24 }}</span></div>
        </div>
    </div>

    <!-- Submissions Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>📋 Danh sách học viên đã nộp bài</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3 px-4">Học viên</th>
                        <th class="py-3 px-4">Thời gian nộp</th>
                        <th class="py-3 px-4">Trạng thái</th>
                        <th class="py-3 px-4 text-center">Điểm số</th>
                        <th class="py-3 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($assignment->submissions ?? [] as $sub)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($sub->user->name ?? 'HV', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">{{ $sub->user->name ?? 'Học viên' }}</p>
                                        <p class="text-xs text-slate-400">{{ $sub->user->email ?? 'student@example.com' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-slate-400">
                                {{ isset($sub->created_at) ? $sub->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4">
                                <x-status-badge :status="$sub->status ?? 'pending'" />
                            </td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-white">
                                {{ $sub->score !== null ? $sub->score . '/10' : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', $sub->id) : url('/admin/submissions/' . $sub->id . '/review') }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 hover:bg-emerald-500/20">
                                    Chấm bài
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/30">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">N</div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">Vũ Đức Nam</p>
                                        <p class="text-xs text-slate-400">nam.vu@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-slate-400">18/03/2026 14:20</td>
                            <td class="py-3 px-4"><x-status-badge status="pending" label="Chờ chấm" /></td>
                            <td class="py-3 px-4 text-center font-mono text-slate-400">-</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', 1) : url('/admin/submissions/1/review') }}" class="inline-flex items-center gap-1 px-3 py-1 rounded text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">Chấm bài</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-purple-400 font-bold flex items-center justify-center text-xs">D</div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">Lê Thuỳ Dung</p>
                                        <p class="text-xs text-slate-400">dung.le@example.com</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-xs font-mono text-slate-400">17/03/2026 19:45</td>
                            <td class="py-3 px-4"><x-status-badge status="approved" label="Đạt (9.5)" /></td>
                            <td class="py-3 px-4 text-center font-mono font-bold text-emerald-400">9.5/10</td>
                            <td class="py-3 px-4 text-right">
                                <a href="{{ Route::has('admin.submissions.show') ? route('admin.submissions.show', 2) : url('/admin/submissions/2') }}" class="inline-flex items-center gap-1 px-3 py-1 rounded text-xs font-semibold bg-slate-700 text-slate-300">Xem lại</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
