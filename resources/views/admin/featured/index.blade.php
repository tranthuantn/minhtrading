@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <span>Quản lý đào tạo</span>
                <span>/</span>
                <span class="text-amber-400">Showcase</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>⭐ Quản Lý Bài Tập & Câu Hỏi Tiêu Biểu</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Danh sách các bài làm xuất sắc và câu hỏi chất lượng cao đã được chọn để vinh danh trong thư viện học tập cho học viên.
            </p>
        </div>
    </div>

    <!-- Filter Bar & Tabs Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-2 p-1 rounded-xl bg-slate-800/80 border border-slate-700/80 self-start">
            <a href="{{ route('admin.featured.index', ['tab' => 'submissions', 'course_id' => $courseId]) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ $tab === 'submissions' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                <span>📝 Bài tập tiêu biểu</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'submissions' ? 'bg-amber-500/30 text-amber-200' : 'bg-slate-700 text-slate-400' }}">
                    {{ $totalFeaturedSubmissions }}
                </span>
            </a>

            <a href="{{ route('admin.featured.index', ['tab' => 'questions', 'course_id' => $courseId]) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ $tab === 'questions' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                <span>❓ Câu hỏi tiêu biểu</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'questions' ? 'bg-amber-500/30 text-amber-200' : 'bg-slate-700 text-slate-400' }}">
                    {{ $totalFeaturedQuestions }}
                </span>
            </a>
        </div>

        <!-- Course Filter Dropdown -->
        <form method="GET" action="{{ route('admin.featured.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="course_id" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white focus:outline-none focus:border-amber-500">
                <option value="">Tất cả khóa học</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>
                        {{ Str::limit($c->title, 30) }}
                    </option>
                @endforeach
            </select>
            @if($courseId)
                <a href="{{ route('admin.featured.index', ['tab' => $tab]) }}" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-400 hover:text-white border border-slate-700 transition">
                    Xóa lọc
                </a>
            @endif
        </form>
    </div>

    <!-- TAB 1: FEATURED SUBMISSIONS -->
    @if($tab === 'submissions')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($featuredSubmissions as $sub)
                <div class="rounded-2xl bg-slate-800/90 border border-amber-500/40 shadow-lg shadow-amber-500/5 flex flex-col justify-between overflow-hidden">
                    <div class="p-5 space-y-4">
                        <!-- Student & Score -->
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center font-bold text-amber-400 text-xs">
                                    {{ strtoupper(substr($sub->student->name ?? 'HV', 0, 2)) }}
                                </div>
                                <div>
                                    <h3 class="text-sm font-bold text-white">{{ $sub->student->name ?? 'Học viên' }}</h3>
                                    <p class="text-[11px] text-slate-400">
                                        {{ $sub->assignment->lesson->course->title ?? 'Khóa học' }} • {{ $sub->assignment->lesson->title ?? 'Bài học' }}
                                    </p>
                                </div>
                            </div>

                            @if($sub->score !== null)
                                <div class="px-2.5 py-1 rounded-lg bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 font-mono font-bold text-xs shrink-0">
                                    ★ {{ $sub->score }}/10
                                </div>
                            @endif
                        </div>

                        <!-- Assignment Title -->
                        <div class="pt-2 border-t border-slate-700/60">
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-0.5">Bài tập:</span>
                            <h4 class="text-sm font-bold text-sky-400 line-clamp-1">
                                {{ $sub->assignment->title ?? 'Thực hành' }}
                            </h4>
                        </div>

                        <!-- Instructor's Featured Reason Note -->
                        @if($sub->featured_note)
                            <div class="p-3 rounded-xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs space-y-1">
                                <div class="font-bold flex items-center gap-1.5 text-amber-300 text-[11px]">
                                    <span>💡 Nhận xét của bạn:</span>
                                </div>
                                <p class="leading-relaxed line-clamp-3">{{ $sub->featured_note }}</p>
                            </div>
                        @endif

                        <!-- Student Submission Content Preview -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-xs text-slate-300 leading-relaxed line-clamp-3">
                            {{ $sub->content }}
                        </div>

                        <!-- Screenshots -->
                        @php
                            $attachments = is_array($sub->attachments) ? $sub->attachments : [];
                        @endphp
                        @if(!empty($attachments))
                            <div class="pt-1">
                                <p class="text-[11px] font-semibold text-slate-400 mb-2">Ảnh đính kèm:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(array_slice($attachments, 0, 3) as $att)
                                        @php
                                            $attUrl = is_array($att) ? ($att['url'] ?? '') : $att;
                                            $url = str_starts_with($attUrl, 'http') ? $attUrl : asset('storage/' . $attUrl);
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" class="block w-20 h-14 rounded-lg overflow-hidden border border-slate-700 hover:border-amber-400 transition">
                                            <img src="{{ $url }}" alt="Chart" class="w-full h-full object-cover">
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Actions Bar -->
                    <div class="p-4 bg-slate-900/60 border-t border-slate-700/60 flex items-center justify-between">
                        <form method="POST" action="{{ route('admin.submissions.toggle-featured', $sub->id) }}">
                            @csrf
                            <button type="submit" 
                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-rose-950/40 text-slate-400 hover:text-rose-300 border border-slate-700 transition">
                                Hủy gắn nhãn tiêu biểu
                            </button>
                        </form>

                        <div class="flex items-center gap-2">
                            <a href="{{ route('admin.submissions.review', $sub->id) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                                Chấm lại
                            </a>
                            <a href="{{ route('admin.submissions.show', $sub->id) }}" 
                               class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-slate-200 transition">
                                Xem chi tiết
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 p-12 text-center rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-2">
                    <span class="text-3xl block">📝</span>
                    <h3 class="text-base font-bold text-white">Chưa có bài tập tiêu biểu nào</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        Bạn có thể đánh giá một bài làm là "Bài tập tiêu biểu" khi chấm bài tại mục Bài tập & Bài nộp.
                    </p>
                </div>
            @endforelse
        </div>

        @if($featuredSubmissions->hasPages())
            <div class="pt-4">
                {{ $featuredSubmissions->links() }}
            </div>
        @endif
    @endif

    <!-- TAB 2: FEATURED QUESTIONS -->
    @if($tab === 'questions')
        <div class="space-y-4">
            @forelse($featuredQuestions as $q)
                <div class="p-6 rounded-2xl bg-slate-800/90 border border-amber-500/40 shadow-lg shadow-amber-500/5 space-y-4">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b border-slate-700/60">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center font-bold text-amber-400 text-xs">
                                {{ strtoupper(substr($q->user->name ?? 'HV', 0, 2)) }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-white">{{ $q->user->name ?? 'Học viên' }}</span>
                                    <span class="text-[11px] text-slate-500">{{ $q->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <p class="text-[11px] text-slate-400">
                                    Khóa học: <span class="text-slate-300">{{ $q->lesson->course->title ?? '' }}</span> • Bài: <span class="text-sky-400">{{ $q->lesson->title ?? '' }}</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                            <form method="POST" action="{{ route('admin.questions.toggle-featured', $q->id) }}">
                                @csrf
                                <button type="submit" 
                                        class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-rose-950/40 text-slate-400 hover:text-rose-300 border border-slate-700 transition">
                                    Hủy tiêu biểu
                                </button>
                            </form>
                            <a href="{{ route('admin.questions.show', $q->id) }}" 
                               class="px-3 py-1 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white transition">
                                Xem & Giải đáp &rarr;
                            </a>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="space-y-2">
                        @if($q->title)
                            <h3 class="text-base font-bold text-sky-400">
                                {{ $q->title }}
                            </h3>
                        @endif
                        <p class="text-xs text-slate-200 leading-relaxed whitespace-pre-line">
                            {{ $q->content }}
                        </p>
                    </div>

                    <!-- Images -->
                    @if(!empty($q->images) && is_array($q->images))
                        <div class="flex flex-wrap gap-2 pt-1">
                            @foreach($q->images as $img)
                                <a href="{{ $img }}" target="_blank" class="block w-24 h-16 rounded-lg overflow-hidden border border-slate-700 hover:border-amber-400 transition">
                                    <img src="{{ $img }}" alt="Ảnh câu hỏi" class="w-full h-full object-cover">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Teacher's Note -->
                    @if($q->featured_note)
                        <div class="p-3.5 rounded-xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-amber-300 text-[11px]">
                                <span>💡 Ghi chú đánh giá:</span>
                            </div>
                            <p class="leading-relaxed">{{ $q->featured_note }}</p>
                        </div>
                    @endif

                    <!-- Instructor Answer Preview -->
                    @php
                        $instructorAns = $q->answers->firstWhere('user.role', 'admin');
                    @endphp
                    @if($instructorAns)
                        <div class="p-4 rounded-xl bg-slate-900 border border-emerald-500/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-400">✓ Lời giải đáp của bạn</span>
                                <span class="text-[10px] text-slate-500">{{ $instructorAns->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-xs text-slate-300 leading-relaxed">
                                {!! nl2br(e(Str::limit($instructorAns->content, 250))) !!}
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="p-12 text-center rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-2">
                    <span class="text-3xl block">❓</span>
                    <h3 class="text-base font-bold text-white">Chưa có câu hỏi tiêu biểu nào</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        Bạn có thể đánh giá một câu hỏi là "Câu hỏi tiêu biểu" khi giải đáp trong mục Hỏi đáp học viên.
                    </p>
                </div>
            @endforelse
        </div>

        @if($featuredQuestions->hasPages())
            <div class="pt-4">
                {{ $featuredQuestions->links() }}
            </div>
        @endif
    @endif
</div>
@endsection

