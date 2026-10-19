@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{
    duplicateOpen: false,
    dueDate: '{{ now()->addDays(7)->format('Y-m-d\TH:i') }}',
    newTitle: '{{ addslashes($assignment->title) }}'
}">
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

        <div class="flex items-center gap-2 flex-wrap">
            <!-- 1-Click Duplicate Button -->
            <button type="button" 
                    @click="duplicateOpen = true"
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-sky-600/20 hover:bg-sky-600/30 text-sky-300 border border-sky-500/40 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                📋 Nhân bản sang khóa khác (1-Click)
            </button>

            <a href="{{ Route::has('admin.assignments.edit') ? route('admin.assignments.edit', $assignment->id ?? 1) : url('/admin/assignments/' . ($assignment->id ?? 1) . '/edit') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa
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
            <div>Tổng bài đã nộp: <span class="text-white font-mono font-bold">{{ isset($submissions) ? $submissions->total() : ($assignment->submissions_count ?? 0) }}</span></div>
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
                    @forelse($submissions ?? [] as $sub)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-xs">
                                        {{ strtoupper(substr($sub->student->name ?? ($sub->user->name ?? 'HV'), 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-white leading-tight">{{ $sub->student->name ?? ($sub->user->name ?? 'Học viên') }}</p>
                                        <p class="text-xs text-slate-400">{{ $sub->student->email ?? ($sub->user->email ?? 'student@example.com') }}</p>
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
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-500">
                                <p class="text-xs text-slate-400">Chưa có học viên nào nộp bài tập này.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($submissions) && method_exists($submissions, 'links'))
            <div class="p-4 border-t border-slate-700/60">
                {{ $submissions->links() }}
            </div>
        @endif
    </div>

    <!-- 1-Click Duplicate Modal -->
    <div x-show="duplicateOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="duplicateOpen" x-transition.opacity class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm transition-opacity" @click="duplicateOpen = false"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div x-show="duplicateOpen" x-transition.scale class="inline-block align-bottom bg-slate-900 border border-slate-700 rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full p-6 space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-lg bg-sky-500/20 text-sky-400 font-bold text-base">📋</span>
                        <div>
                            <h3 class="text-base font-bold text-white">Nhân Bản Đề Bài Tập (1-Click)</h3>
                            <p class="text-xs text-slate-400">Tái sử dụng nội dung đề bài này cho một khóa học khác</p>
                        </div>
                    </div>
                    <button type="button" @click="duplicateOpen = false" class="text-slate-400 hover:text-white text-lg">✕</button>
                </div>

                <!-- Source Assignment Info -->
                <div class="p-3.5 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs space-y-1">
                    <span class="text-slate-400 uppercase font-semibold text-[10px] tracking-wider block">Đề bài đang chọn:</span>
                    <p class="font-bold text-sky-300 text-sm">{{ $assignment->title }}</p>
                    <p class="text-slate-400">Nguồn: {{ $assignment->lesson->course->title ?? 'Khóa học' }} • {{ $assignment->lesson->title ?? 'Bài học' }}</p>
                </div>

                <form action="{{ route('admin.assignments.duplicate', $assignment->id) }}" method="POST" class="space-y-4">
                    @csrf
                    <!-- Target Lesson Selection -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Chọn Khóa học & Bài học đích cần giao đề này <span class="text-rose-400">*</span>
                        </label>
                        <select name="lesson_id" required class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500">
                            <option value="">-- Chọn bài học nhận đề bài này --</option>
                            @if(isset($courses))
                                @foreach($courses as $c)
                                    <optgroup label="Khóa: {{ $c->title }}">
                                        @foreach($c->lessons as $l)
                                            <option value="{{ $l->id }}">{{ $l->title }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <!-- Custom Title if desired -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Tiêu đề bài tập mới (Tùy chọn sửa đổi)
                        </label>
                        <input type="text" name="title" x-model="newTitle" placeholder="Giữ nguyên hoặc nhập tiêu đề mới..." class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500">
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                            Hạn nộp bài mới (Deadline)
                        </label>
                        <input type="datetime-local" name="due_date" x-model="dueDate" class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs font-mono focus:outline-none focus:border-emerald-500">
                    </div>

                    <div class="pt-3 border-t border-slate-800 flex items-center justify-end gap-3">
                        <button type="button" @click="duplicateOpen = false" class="px-4 py-2 rounded-lg text-xs font-medium text-slate-400 hover:text-white bg-slate-800 hover:bg-slate-700 transition">
                            Hủy bỏ
                        </button>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-bold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                            Xác nhận Nhân bản (1-Click)
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
