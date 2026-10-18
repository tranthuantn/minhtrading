@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <span>Quản lý đào tạo</span>
                <span>/</span>
                <span class="text-emerald-400">Hỏi đáp học viên</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>💬 Quản Lý Hỏi Đáp & Giải Thắc Mắc</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Theo dõi và giải đáp các câu hỏi theo từng bài học của học viên, đánh giá câu hỏi tiêu biểu để lưu vào Showcase.
            </p>
        </div>

        <a href="{{ route('admin.featured.index') }}?tab=questions" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition">
            ⭐ Xem Showcase Câu Hỏi Tiêu Biểu
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400">Tổng số câu hỏi</p>
                <p class="text-2xl font-bold text-white mt-1">{{ $stats['total'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400">Chờ giảng viên giải đáp</p>
                <p class="text-2xl font-bold text-rose-400 mt-1">{{ $stats['unanswered'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-center text-rose-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium text-slate-400">Câu hỏi tiêu biểu (Showcase)</p>
                <p class="text-2xl font-bold text-amber-400 mt-1">{{ $stats['featured'] ?? 0 }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
            </div>
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="p-4 rounded-xl bg-slate-800/80 border border-slate-700/80">
        <form method="GET" action="{{ route('admin.questions.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Tìm kiếm</label>
                <input type="text" 
                       name="search" 
                       value="{{ request('search') }}" 
                       placeholder="Tìm theo nội dung, tiêu đề hoặc tên học viên..." 
                       class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
            </div>

            <!-- Course Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Khóa học</label>
                <select name="course_id" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Tất cả khóa học</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>
                            {{ Str::limit($c->title, 25) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Trạng thái giải đáp</label>
                <select name="status" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-xs text-white focus:outline-none focus:border-emerald-500">
                    <option value="">Tất cả trạng thái</option>
                    <option value="unanswered" {{ request('status') === 'unanswered' ? 'selected' : '' }}>Chưa có câu trả lời</option>
                    <option value="answered" {{ request('status') === 'answered' ? 'selected' : '' }}>Đã có câu trả lời</option>
                </select>
            </div>

            <!-- Action buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition text-center">
                    Lọc dữ liệu
                </button>
                <a href="{{ route('admin.questions.index') }}" class="py-2 px-3 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-300 transition text-center">
                    Xóa
                </a>
            </div>
        </form>
    </div>

    <!-- Questions Table -->
    <div class="rounded-xl bg-slate-800/80 border border-slate-700/80 overflow-hidden shadow-lg">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-900/90 text-slate-400 uppercase tracking-wider font-semibold border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">Học viên</th>
                        <th class="px-4 py-3">Bài học / Khóa học</th>
                        <th class="px-4 py-3">Nội dung câu hỏi</th>
                        <th class="px-4 py-3 text-center">Ảnh đính kèm</th>
                        <th class="px-4 py-3 text-center">Phản hồi</th>
                        <th class="px-4 py-3 text-center">Tiêu biểu</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-slate-300">
                    @forelse($questions as $question)
                        <tr class="hover:bg-slate-700/30 transition {{ $question->is_featured ? 'bg-amber-950/10' : '' }}">
                            <td class="px-4 py-3.5 whitespace-nowrap">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-slate-700 border border-slate-600 flex items-center justify-center font-bold text-sky-400 text-xs">
                                        {{ strtoupper(substr($question->user->name ?? 'HV', 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-white">{{ $question->user->name ?? 'Học viên' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $question->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-4 py-3.5">
                                <span class="font-semibold text-slate-200 block">{{ $question->lesson->title ?? 'Bài học' }}</span>
                                <span class="text-[11px] text-slate-400 block">{{ $question->lesson->course->title ?? 'Khóa học' }}</span>
                            </td>

                            <td class="px-4 py-3.5 max-w-xs">
                                @if($question->title)
                                    <p class="font-semibold text-sky-400 truncate">{{ $question->title }}</p>
                                @endif
                                <p class="text-slate-300 line-clamp-2 text-[11px] mt-0.5">{{ $question->content }}</p>
                            </td>

                            <td class="px-4 py-3.5 text-center">
                                @if(!empty($question->images) && count($question->images) > 0)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-500/20 text-sky-400 border border-sky-500/30">
                                        🖼️ {{ count($question->images) }} ảnh
                                    </span>
                                @else
                                    <span class="text-slate-600 text-[11px]">-</span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                @if($question->answers_count > 0)
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        ✓ {{ $question->answers_count }} phản hồi
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        Chưa trả lời
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3.5 text-center whitespace-nowrap">
                                <form method="POST" action="{{ route('admin.questions.toggle-featured', $question->id) }}">
                                    @csrf
                                    <button type="submit" 
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold transition {{ $question->is_featured ? 'bg-amber-500/20 text-amber-300 border border-amber-500/50 hover:bg-rose-950/40 hover:text-rose-300' : 'bg-slate-800 text-slate-400 hover:text-amber-300 border border-slate-700' }}">
                                        ⭐ {{ $question->is_featured ? 'Tiêu biểu' : 'Thường' }}
                                    </button>
                                </form>
                            </td>

                            <td class="px-4 py-3.5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.questions.show', $question->id) }}" 
                                       class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                                        Xem & Trả lời &rarr;
                                    </a>

                                    <form method="POST" action="{{ route('admin.questions.destroy', $question->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa câu hỏi này?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 rounded text-slate-500 hover:text-rose-400 transition" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                Không tìm thấy câu hỏi nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($questions->hasPages())
            <div class="p-4 border-t border-slate-700 bg-slate-900/50">
                {{ $questions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

