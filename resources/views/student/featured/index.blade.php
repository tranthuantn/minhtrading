@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <span>Học tập & Rèn luyện</span>
                <span>/</span>
                <span class="text-amber-400">Góc Tiêu Biểu</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>⭐ Bài Tập & Câu Hỏi Tiêu Biểu (Showcase)</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Tuyển tập những bài tập xuất sắc và câu hỏi chất lượng nhất được Giảng viên đánh giá cao để bạn tham khảo và học hỏi kinh nghiệm.
            </p>
        </div>
    </div>

    <!-- Filter Bar & Tabs Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <!-- Tabs -->
        <div class="flex items-center gap-2 p-1 rounded-xl bg-slate-800/80 border border-slate-700/80 self-start">
            <a href="{{ route('student.featured.index', ['tab' => 'submissions', 'course_id' => $courseId]) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ $tab === 'submissions' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                <span>📝 Bài tập tiêu biểu</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'submissions' ? 'bg-amber-500/30 text-amber-200' : 'bg-slate-700 text-slate-400' }}">
                    {{ $totalFeaturedSubmissions }}
                </span>
            </a>

            <a href="{{ route('student.featured.index', ['tab' => 'questions', 'course_id' => $courseId]) }}" 
               class="flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-bold transition {{ $tab === 'questions' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm' : 'text-slate-400 hover:text-white' }}">
                <span>❓ Câu hỏi tiêu biểu</span>
                <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'questions' ? 'bg-amber-500/30 text-amber-200' : 'bg-slate-700 text-slate-400' }}">
                    {{ $totalFeaturedQuestions }}
                </span>
            </a>
        </div>

        <!-- Course Filter Dropdown -->
        <form method="GET" action="{{ route('student.featured.index') }}" class="flex items-center gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <select name="course_id" 
                    onchange="this.form.submit()" 
                    class="px-3.5 py-2 rounded-xl bg-slate-800 border border-slate-700 text-xs text-white focus:outline-none focus:border-amber-500">
                <option value="">Tất cả khóa học của tôi</option>
                @foreach($enrolledCourses as $c)
                    <option value="{{ $c->id }}" {{ $courseId == $c->id ? 'selected' : '' }}>
                        {{ Str::limit($c->title, 30) }}
                    </option>
                @endforeach
            </select>
            @if($courseId)
                <a href="{{ route('student.featured.index', ['tab' => $tab]) }}" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-400 hover:text-white border border-slate-700 transition">
                    Xóa lọc
                </a>
            @endif
        </form>
    </div>

    <!-- TAB 1: FEATURED SUBMISSIONS -->
    @if($tab === 'submissions')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($featuredSubmissions as $sub)
                <div class="rounded-2xl bg-slate-800/90 border border-amber-500/40 shadow-lg shadow-amber-500/5 flex flex-col justify-between overflow-hidden group hover:border-amber-400/80 transition-all duration-300">
                    <div class="p-5 space-y-4">
                        <!-- Top Row: Student & Score -->
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
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 block mb-0.5">Bài tập thực hành:</span>
                            <h4 class="text-sm font-bold text-sky-400 line-clamp-1">
                                {{ $sub->assignment->title ?? 'Thực hành Backtest' }}
                            </h4>
                        </div>

                        <!-- Instructor's Featured Reason Note -->
                        @if($sub->featured_note)
                            <div class="p-3 rounded-xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs space-y-1">
                                <div class="font-bold flex items-center gap-1.5 text-amber-300 text-[11px]">
                                    <span>💡 Nhận xét của Giảng viên:</span>
                                </div>
                                <p class="leading-relaxed line-clamp-3">{{ $sub->featured_note }}</p>
                            </div>
                        @endif

                        <!-- Student Submission Content Preview -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 text-xs text-slate-300 leading-relaxed line-clamp-3">
                            {{ $sub->content }}
                        </div>

                        <!-- Screenshot Gallery (If attachments exist) -->
                        @php
                            $attachments = is_array($sub->attachments) ? $sub->attachments : [];
                        @endphp
                        @if(!empty($attachments))
                            <div class="pt-1">
                                <p class="text-[11px] font-semibold text-slate-400 mb-2">Ảnh biểu đồ phân tích:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(array_slice($attachments, 0, 3) as $att)
                                        @php
                                            $attUrl = is_array($att) ? ($att['url'] ?? '') : $att;
                                            $url = str_starts_with($attUrl, 'http') ? $attUrl : asset('storage/' . $attUrl);
                                        @endphp
                                        <a href="{{ $url }}" target="_blank" class="block w-20 h-14 rounded-lg overflow-hidden border border-slate-700 hover:border-amber-400 transition group/img">
                                            <img src="{{ $url }}" alt="Chart" class="w-full h-full object-cover group-hover/img:scale-105 transition-transform">
                                        </a>
                                    @endforeach
                                    @if(count($attachments) > 3)
                                        <span class="w-14 h-14 rounded-lg bg-slate-900 border border-slate-800 flex items-center justify-center text-xs text-slate-400 font-semibold">
                                            +{{ count($attachments) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Bottom Action -->
                    <div class="p-4 bg-slate-900/60 border-t border-slate-700/60 flex items-center justify-between">
                        <span class="text-[11px] text-slate-500 font-medium">
                            Được vinh danh lúc {{ $sub->featured_at ? $sub->featured_at->format('d/m/Y') : $sub->updated_at->format('d/m/Y') }}
                        </span>

                        <a href="{{ route('student.submissions.show', $sub->id) }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition">
                            Chi tiết bài nộp &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="md:col-span-2 p-12 text-center rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-2">
                    <span class="text-3xl block">📝</span>
                    <h3 class="text-base font-bold text-white">Chưa có bài tập tiêu biểu nào</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        Khi các bài làm xuất sắc của học viên được Giảng viên đánh giá và vinh danh, chúng sẽ xuất hiện tại đây để cả lớp cùng học hỏi.
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
                <div class="p-6 rounded-2xl bg-slate-800/90 border border-amber-500/40 shadow-lg shadow-amber-500/5 space-y-4 hover:border-amber-400 transition">
                    <!-- Top: Student, Date & Course -->
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
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                ⭐ CÂU HỎI TIÊU BIỂU
                            </span>
                            <span class="px-2 py-0.5 rounded text-xs bg-slate-900 text-slate-400 border border-slate-700">
                                {{ $q->answers_count }} phản hồi
                            </span>
                        </div>
                    </div>

                    <!-- Question Title & Content -->
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
                                <a href="{{ $img }}" target="_blank" class="block w-24 h-16 rounded-lg overflow-hidden border border-slate-700 hover:border-amber-400 transition group relative">
                                    <img src="{{ $img }}" alt="Ảnh câu hỏi" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                </a>
                            @endforeach
                        </div>
                    @endif

                    <!-- Teacher's Featured Evaluation Reason -->
                    @if($q->featured_note)
                        <div class="p-3.5 rounded-xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs space-y-1">
                            <div class="font-bold flex items-center gap-1.5 text-amber-300 text-[11px]">
                                <span>💡 Đánh giá của Giảng viên:</span>
                            </div>
                            <p class="leading-relaxed">{{ $q->featured_note }}</p>
                        </div>
                    @endif

                    <!-- Instructor's Official Answer Preview (if exists) -->
                    @php
                        $instructorAns = $q->answers->firstWhere('user.role', 'admin');
                    @endphp
                    @if($instructorAns)
                        <div class="p-4 rounded-xl bg-slate-900 border border-emerald-500/30 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-bold text-emerald-400">Giảng viên ({{ $instructorAns->user->name ?? 'Admin' }})</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30">✓ Giải đáp chuẩn</span>
                                </div>
                                <span class="text-[10px] text-slate-500">{{ $instructorAns->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="text-xs text-slate-300 leading-relaxed">
                                {!! nl2br(e(Str::limit($instructorAns->content, 250))) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Action Link -->
                    <div class="pt-2 flex justify-end">
                        <a href="{{ route('student.questions.show', $q->id) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-500 text-white transition shadow-sm">
                            Xem trọn vẹn thảo luận & câu trả lời &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-2">
                    <span class="text-3xl block">❓</span>
                    <h3 class="text-base font-bold text-white">Chưa có câu hỏi tiêu biểu nào</h3>
                    <p class="text-xs text-slate-400 max-w-md mx-auto">
                        Khi các câu hỏi hay mang tính thực chiến cao được Giảng viên giải đáp và gắn nhãn tiêu biểu, chúng sẽ xuất hiện tại đây.
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

