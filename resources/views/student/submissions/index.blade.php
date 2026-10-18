@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📋 Lịch Sử Bài Đã Nộp</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Theo dõi trạng thái chấm điểm, nhận xét của giảng viên và điểm số bài thực hành</p>
        </div>

        <a href="{{ Route::has('student.assignments.index') ? route('student.assignments.index') : url('/student/assignments') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-500 text-white shadow transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Làm bài tập mới
        </a>
    </div>

    <!-- Submissions Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3.5 px-4">Bài tập</th>
                        <th class="py-3.5 px-4">Ngày nộp</th>
                        <th class="py-3.5 px-4">Trạng thái chấm</th>
                        <th class="py-3.5 px-4 text-center">Điểm số</th>
                        <th class="py-3.5 px-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($submissions ?? [] as $sub)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">
                                <a href="{{ Route::has('student.submissions.show') ? route('student.submissions.show', $sub->id) : url('/student/submissions/' . $sub->id) }}" class="hover:text-sky-400 block">
                                    {{ $sub->assignment->title ?? 'Bài tập thực hành' }}
                                </a>
                                <span class="text-xs text-slate-400 font-normal">{{ $sub->assignment->course->title ?? '' }}</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">
                                {{ isset($sub->created_at) ? $sub->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="$sub->status ?? 'pending'" />
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-white">
                                @if($sub->score !== null)
                                    <span class="{{ $sub->score >= 8 ? 'text-emerald-400' : ($sub->score >= 5 ? 'text-sky-400' : 'text-rose-400') }}">
                                        {{ $sub->score }}/10
                                    </span>
                                @else
                                    <span class="text-slate-500 font-normal">Chưa có</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('student.submissions.show') ? route('student.submissions.show', $sub->id) : url('/student/submissions/' . $sub->id) }}" 
                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition-colors">
                                    Chi tiết & Nhận xét
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">
                                Backtest 15 lệnh mô hình Pin Bar EURUSD
                                <span class="text-xs text-slate-400 font-normal block">Khóa: Price Action Thực Chiến</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">18/03/2026 14:20</td>
                            <td class="py-3.5 px-4"><x-status-badge status="pending" label="Chờ chấm" /></td>
                            <td class="py-3.5 px-4 text-center font-mono text-slate-500">Chưa có</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('student.submissions.show') ? route('student.submissions.show', 1) : url('/student/submissions/1') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 text-slate-200">Chi tiết</a>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4 font-semibold text-white">
                                Xác định cấu trúc Market Structure H4 XAUUSD
                                <span class="text-xs text-slate-400 font-normal block">Khóa: Price Action Thực Chiến</span>
                            </td>
                            <td class="py-3.5 px-4 text-xs font-mono text-slate-400">14/03/2026 21:05</td>
                            <td class="py-3.5 px-4"><x-status-badge status="approved" label="Đạt (9.0)" /></td>
                            <td class="py-3.5 px-4 text-center font-mono font-bold text-emerald-400">9.0/10</td>
                            <td class="py-3.5 px-4 text-right">
                                <a href="{{ Route::has('student.submissions.show') ? route('student.submissions.show', 2) : url('/student/submissions/2') }}" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 text-slate-200">Chi tiết</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($submissions) && method_exists($submissions, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
