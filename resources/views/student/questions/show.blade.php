@extends('layouts.student')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Breadcrumbs -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div class="flex items-center gap-2 text-xs text-slate-400">
            @if($question->lesson)
                <a href="{{ route('student.courses.index') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $question->lesson->course_id) }}" class="hover:text-white">{{ $question->lesson->course->title ?? 'Khóa học' }}</a>
                <span>/</span>
                <a href="{{ route('student.courses.lesson', ['course' => $question->lesson->course_id, 'lesson' => $question->lesson_id]) }}" class="hover:text-white">{{ $question->lesson->title }}</a>
                <span>/</span>
                <span class="text-sky-400">Hỏi đáp #{{ $question->id }}</span>
            @else
                <a href="{{ route('student.assignments.index', ['tab' => 'questions']) }}" class="hover:text-white">Bài tập & Hỏi đáp</a>
                <span>/</span>
                <span class="text-sky-400">Hỏi đáp ngoài bài học #{{ $question->id }}</span>
            @endif
        </div>

        @if($question->lesson)
            <a href="{{ route('student.courses.lesson', ['course' => $question->lesson->course_id, 'lesson' => $question->lesson_id]) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                &larr; Quay lại bài học
            </a>
        @else
            <a href="{{ route('student.assignments.index', ['tab' => 'questions']) }}" 
               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                &larr; Quay lại danh sách hỏi đáp
            </a>
        @endif
    </div>

    <!-- Main Question Box -->
    <div class="p-6 rounded-2xl bg-slate-800/80 backdrop-blur border {{ $question->is_featured ? 'border-amber-500/50 bg-gradient-to-r from-amber-950/20 to-slate-800/80 shadow-lg shadow-amber-500/5' : 'border-slate-700' }} space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center font-bold text-sky-400">
                    {{ strtoupper(substr($question->user->name ?? 'HV', 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">{{ $question->user->name ?? 'Học viên' }}</h3>
                    <p class="text-xs text-slate-400">Đăng lúc: {{ $question->created_at->format('H:i d/m/Y') }} ({{ $question->created_at->diffForHumans() }})</p>
                    <div class="mt-1">
                        @if($question->lesson)
                            <span class="inline-flex items-center gap-1 text-[11px] text-slate-400">
                                📖 Bài: <strong class="text-slate-300">{{ $question->lesson->title }}</strong> • Khóa: {{ $question->lesson->course->title ?? '' }}
                            </span>
                        @elseif($question->course)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-sky-500/15 text-sky-400 border border-sky-500/30">
                                🌐 Ngoài bài học (Khóa: {{ $question->course->title }})
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-700 text-slate-300 border border-slate-600">
                                🌐 Ngoài bài học (Hỏi đáp chung)
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            @if($question->is_featured)
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                    ⭐ CÂU HỎI TIÊU BIỂU
                </div>
            @endif
        </div>

        @if($question->title)
            <h2 class="text-lg font-bold text-sky-400 pt-2 border-t border-slate-700/60">
                {{ $question->title }}
            </h2>
        @endif

        <div class="text-sm text-slate-200 leading-relaxed whitespace-pre-line">
            {{ $question->content }}
        </div>

        <!-- Screenshots / Images -->
        @if(!empty($question->images) && is_array($question->images))
            <div class="pt-2">
                <p class="text-xs font-semibold text-slate-400 mb-2">Hình ảnh đính kèm:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($question->images as $img)
                        <a href="{{ $img }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-700 hover:border-sky-500 transition group relative">
                            <img src="{{ $img }}" alt="Ảnh câu hỏi" class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                <span class="px-2 py-1 rounded bg-slate-900/80 text-white text-xs font-medium">Phóng to &nearr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Teacher's Featured Evaluation Note -->
        @if($question->is_featured && $question->featured_note)
            <div class="p-4 rounded-xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs space-y-1">
                <div class="font-bold flex items-center gap-1.5 text-amber-300">
                    <span>💡 Đánh giá của Giảng viên:</span>
                </div>
                <p class="leading-relaxed">{{ $question->featured_note }}</p>
            </div>
        @endif
    </div>

    <!-- Replies & Answers Thread -->
    <div class="space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span>💬 Phản hồi & Giải đáp</span>
            <span class="text-xs px-2 py-0.5 rounded-full bg-slate-800 text-slate-400 border border-slate-700">
                {{ $question->answers->count() }}
            </span>
        </h3>

        @forelse($question->answers as $ans)
            @php
                $isInstructor = ($ans->user->role ?? '') === 'admin';
            @endphp
            <div class="p-5 rounded-xl {{ $isInstructor ? 'bg-slate-800/90 border border-emerald-500/40 shadow-md shadow-emerald-500/5' : 'bg-slate-900 border border-slate-800' }} space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full {{ $isInstructor ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-800 text-slate-300 border border-slate-700' }} flex items-center justify-center font-bold text-xs">
                            {{ strtoupper(substr($ans->user->name ?? 'U', 0, 2)) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold {{ $isInstructor ? 'text-emerald-400' : 'text-white' }}">{{ $ans->user->name ?? 'Người dùng' }}</span>
                                @if($isInstructor)
                                    <span class="px-2 py-0.2 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        GIẢNG VIÊN
                                    </span>
                                @endif
                            </div>
                            <p class="text-[11px] text-slate-500">{{ $ans->created_at->format('H:i d/m/Y') }} ({{ $ans->created_at->diffForHumans() }})</p>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-line pl-11">
                    {{ $ans->content }}
                </div>

                @if(!empty($ans->images) && is_array($ans->images))
                    <div class="pl-11 pt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($ans->images as $ansImg)
                            <a href="{{ $ansImg }}" target="_blank" class="block rounded-lg overflow-hidden border border-slate-700 hover:border-sky-500 transition group relative">
                                <img src="{{ $ansImg }}" alt="Ảnh đính kèm" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-200">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="p-6 text-center rounded-xl bg-slate-900 border border-slate-800 text-slate-400 text-xs">
                Chưa có phản hồi nào. Hãy viết phản hồi bên dưới!
            </div>
        @endforelse
    </div>

    <!-- Reply Form -->
    <div class="p-6 rounded-2xl bg-slate-800/60 border border-slate-700 space-y-4">
        <h4 class="text-sm font-bold text-white flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            Viết phản hồi / Thảo luận thêm
        </h4>

        <form action="{{ route('student.questions.reply', $question->id) }}" 
              method="POST" 
              enctype="multipart/form-data" 
              class="space-y-4">
            @csrf

            <div>
                <textarea name="content" 
                          rows="4" 
                          required
                          placeholder="Nhập nội dung phản hồi của bạn..."
                          class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-sky-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-400 mb-1">Đính kèm ảnh minh họa (tối đa 5 ảnh)</label>
                <input type="file" 
                       name="images[]" 
                       multiple 
                       accept="image/*"
                       class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-sky-400 hover:file:bg-slate-600">
            </div>

            <div class="flex justify-end">
                <button type="submit" 
                        class="px-5 py-2.5 rounded-xl text-xs font-semibold text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/20 transition">
                    Gửi phản hồi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

