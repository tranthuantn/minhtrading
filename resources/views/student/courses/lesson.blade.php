@extends('layouts.student')

@section('styles')
<style>
    /* Styling for rendered rich text content inside lesson */
    .lesson-content {
        color: #e2e8f0;
        font-size: 0.95rem;
        line-height: 1.75;
    }
    .lesson-content h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-top: 1.5rem;
        margin-bottom: 0.75rem;
    }
    .lesson-content h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #38bdf8;
        margin-top: 1.25rem;
        margin-bottom: 0.5rem;
    }
    .lesson-content h3 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #34d399;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }
    .lesson-content p {
        margin-bottom: 1rem;
    }
    .lesson-content ul {
        list-style-type: disc;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .lesson-content ol {
        list-style-type: decimal;
        padding-left: 1.5rem;
        margin-bottom: 1rem;
    }
    .lesson-content li {
        margin-bottom: 0.25rem;
    }
    .lesson-content strong {
        color: #ffffff;
        font-weight: 600;
    }
    .lesson-content blockquote {
        border-left: 4px solid #38bdf8;
        padding-left: 1rem;
        margin: 1rem 0;
        color: #94a3b8;
        font-style: italic;
        background-color: #0f172a;
        padding: 0.75rem 1rem;
        border-radius: 0 0.5rem 0.5rem 0;
    }
    .lesson-content pre {
        background-color: #020617;
        border: 1px solid #334155;
        border-radius: 0.5rem;
        padding: 1rem;
        margin: 1rem 0;
        overflow-x: auto;
        font-family: monospace;
        font-size: 0.85rem;
    }
    .lesson-content img {
        max-width: 100%;
        height: auto;
        border-radius: 0.75rem;
        margin: 1.5rem auto;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.4);
        border: 1px solid #334155;
        display: block;
    }
</style>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header / Breadcrumbs -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('student.courses.index') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <a href="{{ route('student.courses.show', $course->id) }}" class="hover:text-white">{{ $course->title }}</a>
                <span>/</span>
                <span class="text-sky-400 font-mono">Bài #{{ $lesson->sort_order }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $lesson->title }}</h1>
        </div>

        <a href="{{ route('student.courses.show', $course->id) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path></svg>
            Mục lục bài học
        </a>
    </div>

    <!-- Video Embed (If Available) -->
    @php
        $embedUrl = $lesson->embed_video_url;
        $platform = $lesson->video_platform;
    @endphp

    @if(!empty($embedUrl))
        <div class="space-y-2">
            <div class="rounded-xl overflow-hidden bg-slate-950 border border-slate-800 shadow-2xl aspect-video relative">
                @if($platform === 'direct')
                    <video controls class="w-full h-full">
                        <source src="{{ $embedUrl }}">
                        Trình duyệt của bạn không hỗ trợ thẻ video HTML5.
                    </video>
                @else
                    <iframe 
                        src="{{ $embedUrl }}" 
                        title="{{ $lesson->title }}" 
                        class="w-full h-full border-0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                    </iframe>
                @endif
            </div>

            <!-- Video Info & External Link Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 px-3 py-2 rounded-lg bg-slate-800/60 border border-slate-700/60 text-xs">
                <div class="flex items-center gap-2 text-slate-300">
                    <span class="inline-flex items-center gap-1 font-semibold text-sky-400">
                        @if($platform === 'drive')
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 24 24"><path d="M19.35 10.04C18.67 6.59 15.64 4 12 4 9.11 4 6.6 5.64 5.35 8.04 2.34 8.36 0 10.91 0 14c0 3.31 2.69 6 6 6h13c2.76 0 5-2.24 5-5 0-2.64-2.05-4.78-4.65-4.96zM17 13l-5 5-5-5h3V9h4v4h3z"/></svg>
                            Google Drive Video
                        @elseif($platform === 'youtube')
                            <svg class="w-4 h-4 text-rose-400" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            YouTube Video
                        @elseif($platform === 'vimeo')
                            Vimeo Video
                        @else
                            Video bài giảng
                        @endif
                    </span>
                    @if($platform === 'drive')
                        <span class="text-[11px] text-slate-400 hidden md:inline">• Nếu video không chạy, vui lòng kiểm tra file Drive đã bật quyền "Bất kỳ ai có liên kết đều xem được"</span>
                    @endif
                </div>

                <a href="{{ $lesson->video_url }}" 
                   target="_blank" 
                   rel="noopener noreferrer" 
                   class="inline-flex items-center gap-1.5 font-semibold text-sky-400 hover:text-sky-300 transition text-[11px] self-start sm:self-auto">
                    <span>Mở video trong tab mới</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                </a>
            </div>
        </div>
    @endif

    <!-- Lesson Rich Text Content -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 sm:p-8 shadow-xl space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
            <h2 class="text-base font-bold text-white flex items-center gap-2">
                <span class="text-sky-400">📖</span>
                Nội Dung Giáo Trình Bài Học
            </h2>
            <span class="text-xs text-slate-400 font-mono">Cập nhật: {{ $lesson->updated_at ? $lesson->updated_at->format('d/m/Y') : '' }}</span>
        </div>

        <div class="lesson-content">
            {!! $lesson->content !!}
        </div>

        <!-- Attachment Download Links -->
        @if(!empty($lesson->attachments) && count($lesson->attachments) > 0)
            <div class="pt-6 border-t border-slate-700/60">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3 flex items-center gap-2">
                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                    Tài liệu bài giảng đính kèm
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($lesson->attachments as $att)
                        @php
                            $attUrl = str_starts_with($att['url'] ?? '', 'http') ? $att['url'] : asset('storage/' . ($att['url'] ?? ''));
                        @endphp
                        <a href="{{ $attUrl }}" target="_blank" download class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border border-slate-700 hover:border-sky-500 text-slate-300 hover:text-white transition group">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <svg class="w-4 h-4 text-sky-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <span class="text-xs font-semibold truncate">{{ $att['name'] ?? 'Tai_lieu.pdf' }}</span>
                            </div>
                            <span class="text-[11px] text-sky-400 font-semibold shrink-0 group-hover:translate-x-0.5 transition-transform">
                                Tải về &darr;
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Related Assignment Card -->
        @if(isset($assignments) && count($assignments) > 0)
            <div class="pt-6 border-t border-slate-700/60 space-y-3">
                <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    Bài tập thực hành cho bài học này
                </h4>

                @foreach($assignments as $asm)
                    @php
                        $mySub = $asm->submissions->first();
                    @endphp
                    <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-950/40 to-slate-900 border border-emerald-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2">
                                <h5 class="text-sm font-bold text-white">{{ $asm->title }}</h5>
                                @if($mySub)
                                    <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/40">
                                        ✓ Đã nộp
                                    </span>
                                @else
                                    <span class="text-[10px] px-2 py-0.5 rounded font-semibold bg-amber-500/20 text-amber-400 border border-amber-500/40">
                                        Chưa nộp
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-1 line-clamp-1">{{ $asm->description }}</p>
                        </div>
                        <a href="{{ route('student.assignments.show', $asm->id) }}" 
                           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 transition-all self-start sm:self-auto shrink-0">
                            {{ $mySub ? 'Xem bài đã nộp' : 'Làm bài tập ngay' }} &rarr;
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Next / Prev Lesson Navigation -->
        <div class="pt-6 border-t border-slate-700/60 flex items-center justify-between gap-4">
            @if($prevLesson)
                <a href="{{ route('student.courses.lesson', ['course' => $course->id, 'lesson' => $prevLesson->id]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-slate-900 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 transition">
                    &larr; Bài trước: {{ Str::limit($prevLesson->title, 25) }}
                </a>
            @else
                <div></div>
            @endif

            @if($nextLesson)
                <a href="{{ route('student.courses.lesson', ['course' => $course->id, 'lesson' => $nextLesson->id]) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-sky-600 hover:bg-sky-500 text-white text-xs font-semibold shadow-lg shadow-sky-600/20 transition">
                    Bài tiếp theo: {{ Str::limit($nextLesson->title, 25) }} &rarr;
                </a>
            @else
                <a href="{{ route('student.courses.show', $course->id) }}" 
                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition">
                    Hoàn thành khóa học &rarr;
                </a>
            @endif
        </div>
    </div>

    <!-- Q&A / Thảo luận theo bài học -->
    <div class="bg-slate-800/60 backdrop-blur rounded-2xl border border-slate-700/60 p-6 space-y-6" id="qa-section">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-700/60">
            <div>
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    Hỏi đáp & Thảo luận về bài học này
                </h3>
                <p class="text-xs text-slate-400 mt-1">
                    Đặt câu hỏi hoặc đính kèm ảnh biểu đồ chart để được Giảng viên giải đáp chi tiết.
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                {{ count($questions ?? []) }} câu hỏi
            </span>
        </div>

        <!-- Form đặt câu hỏi mới -->
        <div class="p-5 rounded-xl bg-slate-900/80 border border-slate-700" x-data="{ openForm: false }">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-sky-500/20 text-sky-400 font-bold flex items-center justify-center text-xs border border-sky-500/30">
                        {{ strtoupper(substr(auth()->user()->name ?? 'HV', 0, 2)) }}
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-white">Bạn có thắc mắc về nội dung bài giảng?</p>
                        <p class="text-[11px] text-slate-400">Giảng viên sẽ phản hồi trực tiếp kèm nhận xét biểu đồ.</p>
                    </div>
                </div>
                <button type="button" @click="openForm = !openForm" class="px-3.5 py-1.5 rounded-lg text-xs font-semibold bg-sky-600 hover:bg-sky-500 text-white transition flex items-center gap-1.5">
                    <span x-text="openForm ? 'Đóng lại' : '+ Đặt câu hỏi mới'"></span>
                </button>
            </div>

            <form action="{{ route('student.lessons.questions.store', $lesson->id) }}" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  x-show="openForm" 
                  x-collapse
                  class="mt-4 pt-4 border-t border-slate-800 space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tiêu đề câu hỏi (tùy chọn)</label>
                    <input type="text" 
                           name="title" 
                           placeholder="Ví dụ: Thắc mắc về cách xác định Swing High/Low trong khung H4..." 
                           class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-sky-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nội dung câu hỏi <span class="text-rose-400">*</span></label>
                    <textarea name="content" 
                              rows="4" 
                              required
                              placeholder="Mô tả chi tiết câu hỏi hoặc phân tích lệnh bạn chưa hiểu rõ..."
                              class="w-full px-3.5 py-2.5 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-sky-500"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">
                        Đính kèm ảnh minh họa / biểu đồ (chọn tối đa 5 ảnh, định dạng jpg, png, webp)
                    </label>
                    <input type="file" 
                           name="images[]" 
                           multiple 
                           accept="image/*"
                           class="block w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-800 file:text-sky-400 hover:file:bg-slate-700">
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" @click="openForm = false" class="px-4 py-2 rounded-lg text-xs font-semibold text-slate-400 hover:text-white bg-slate-800">
                        Hủy
                    </button>
                    <button type="submit" class="px-5 py-2 rounded-lg text-xs font-semibold text-white bg-sky-600 hover:bg-sky-500 shadow-md shadow-sky-600/20">
                        Gửi câu hỏi
                    </button>
                </div>
            </form>
        </div>

        <!-- Danh sách các câu hỏi đã có -->
        @if(isset($questions) && count($questions) > 0)
            <div class="space-y-4">
                @foreach($questions as $q)
                    <div class="p-5 rounded-xl bg-slate-900 border {{ $q->is_featured ? 'border-amber-500/50 bg-gradient-to-r from-amber-950/20 to-slate-900' : 'border-slate-800' }} space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center text-xs font-bold text-slate-300">
                                    {{ strtoupper(substr($q->user->name ?? 'HV', 0, 2)) }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-white">{{ $q->user->name ?? 'Học viên' }}</span>
                                        <span class="text-[11px] text-slate-500">{{ $q->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if($q->title)
                                        <h4 class="text-sm font-semibold text-sky-400 mt-0.5">{{ $q->title }}</h4>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($q->is_featured)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                        ⭐ Tiêu biểu
                                    </span>
                                @endif
                                <span class="px-2 py-0.5 rounded text-[11px] font-medium bg-slate-800 text-slate-400 border border-slate-700">
                                    {{ $q->answers->count() }} câu trả lời
                                </span>
                            </div>
                        </div>

                        <!-- Nội dung câu hỏi -->
                        <div class="text-xs text-slate-300 leading-relaxed pl-11">
                            {!! nl2br(e($q->content)) !!}
                        </div>

                        <!-- Ảnh đính kèm của câu hỏi -->
                        @if(!empty($q->images) && is_array($q->images))
                            <div class="flex flex-wrap gap-2 pl-11 pt-1">
                                @foreach($q->images as $img)
                                    <a href="{{ $img }}" target="_blank" class="block rounded-lg overflow-hidden border border-slate-700 hover:border-sky-500 transition group relative">
                                        <img src="{{ $img }}" alt="Ảnh câu hỏi" class="w-24 h-16 object-cover group-hover:scale-105 transition-transform">
                                        <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif

                        <!-- Ghi chú nếu là câu hỏi tiêu biểu -->
                        @if($q->is_featured && $q->featured_note)
                            <div class="ml-11 p-3 rounded-lg bg-amber-950/30 border border-amber-500/30 text-amber-200 text-xs flex items-start gap-2">
                                <span class="font-bold shrink-0">💡 Đánh giá của Giảng viên:</span>
                                <span>{{ $q->featured_note }}</span>
                            </div>
                        @endif

                        <!-- Câu trả lời gần nhất của Giảng viên (nếu có) -->
                        @php
                            $instructorAnswer = $q->answers->firstWhere('user.role', 'admin');
                        @endphp
                        @if($instructorAnswer)
                            <div class="ml-11 p-3.5 rounded-xl bg-slate-800/80 border border-emerald-500/30 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-emerald-400">Giảng viên ({{ $instructorAnswer->user->name ?? 'Admin' }})</span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 font-semibold border border-emerald-500/30">✓ Giải đáp</span>
                                    </div>
                                    <span class="text-[10px] text-slate-500">{{ $instructorAnswer->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="text-xs text-slate-300 leading-relaxed">
                                    {!! nl2br(e(Str::limit($instructorAnswer->content, 200))) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Action bar -->
                        <div class="pl-11 pt-2 flex items-center justify-between">
                            <span class="text-[11px] text-slate-500">
                                @if($q->answers->count() > 0)
                                    Đã có {{ $q->answers->count() }} lượt phản hồi
                                @else
                                    Đang chờ Giảng viên phản hồi...
                                @endif
                            </span>

                            <a href="{{ route('student.questions.show', $q->id) }}" 
                               class="inline-flex items-center gap-1.5 text-xs font-semibold text-sky-400 hover:text-sky-300 transition">
                                <span>Xem toàn bộ thảo luận & Trả lời</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-8 text-center rounded-xl bg-slate-900 border border-slate-800 text-slate-400 text-xs">
                Chưa có câu hỏi nào cho bài học này. Hãy là người đầu tiên đặt câu hỏi cho Giảng viên!
            </div>
        @endif
    </div>
</div>
@endsection
