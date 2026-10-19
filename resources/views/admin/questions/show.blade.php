@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.questions.index') }}" class="hover:text-white">Hỏi đáp</a>
                <span>/</span>
                <span class="text-emerald-400">Chi tiết & Trả lời câu hỏi</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>Giải Đáp Câu Hỏi #{{ $question->id }}</span>
                @if($question->is_featured)
                    <span class="text-xs px-2.5 py-0.5 rounded-full font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        ⭐ Tiêu Biểu (Showcase)
                    </span>
                @endif
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                @if($question->lesson)
                    Khóa học: <span class="text-white font-medium">{{ $question->lesson->course->title ?? 'Khóa học' }}</span> • Bài học: <span class="text-sky-400 font-semibold">{{ $question->lesson->title }}</span>
                @elseif($question->course)
                    Khóa học: <span class="text-white font-medium">{{ $question->course->title }}</span> • <span class="px-2 py-0.5 rounded bg-sky-500/20 text-sky-400 border border-sky-500/30 text-xs font-semibold">🌐 Ngoài bài học</span>
                @else
                    <span class="px-2 py-0.5 rounded bg-slate-700 text-slate-300 border border-slate-600 text-xs font-semibold">🌐 Ngoài bài học (Hỏi đáp chung)</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('admin.questions.toggle-featured', $question->id) }}">
                @csrf
                <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold {{ $question->is_featured ? 'bg-slate-800 hover:bg-rose-950/40 hover:text-rose-300 text-slate-300 border border-slate-700' : 'bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40' }} transition">
                    ⭐ {{ $question->is_featured ? 'Hủy nhãn Tiêu Biểu' : 'Đánh giá là Tiêu Biểu' }}
                </button>
            </form>

            <a href="{{ route('admin.questions.index') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Question Content Box -->
    <div class="p-6 rounded-2xl bg-slate-800/90 border {{ $question->is_featured ? 'border-amber-500/50 bg-gradient-to-r from-amber-950/20 to-slate-800/90' : 'border-slate-700' }} shadow-lg space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-slate-700/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-900 border border-slate-700 flex items-center justify-center font-bold text-sky-400">
                    {{ strtoupper(substr($question->user->name ?? 'HV', 0, 2)) }}
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">{{ $question->user->name ?? 'Học viên' }}</h3>
                    <p class="text-xs text-slate-400">{{ $question->user->email ?? '' }} • Đăng lúc: {{ $question->created_at->format('d/m/Y H:i') }} ({{ $question->created_at->diffForHumans() }})</p>
                </div>
            </div>

            <span class="text-xs px-2.5 py-1 rounded bg-slate-900 text-slate-300 border border-slate-700">
                {{ $question->answers->count() }} câu trả lời
            </span>
        </div>

        @if($question->title)
            <h2 class="text-lg font-bold text-sky-400">
                {{ $question->title }}
            </h2>
        @endif

        <div class="text-sm text-slate-200 leading-relaxed whitespace-pre-line bg-slate-900/60 p-4 rounded-xl border border-slate-800">
            {{ $question->content }}
        </div>

        <!-- Screenshots -->
        @if(!empty($question->images) && is_array($question->images))
            <div class="pt-2">
                <p class="text-xs font-semibold text-slate-400 mb-2">Ảnh biểu đồ học viên đính kèm:</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                    @foreach($question->images as $img)
                        <a href="{{ $img }}" target="_blank" class="block rounded-xl overflow-hidden border border-slate-700 hover:border-sky-500 transition group relative">
                            <img src="{{ $img }}" alt="Ảnh câu hỏi" class="w-full h-36 object-cover group-hover:scale-105 transition-transform duration-300">
                            <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                                <span class="px-2 py-1 rounded bg-slate-900/80 text-white text-xs font-medium">Xem kích thước gốc &nearr;</span>
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
                    <span>💡 Ghi chú đánh giá tiêu biểu:</span>
                </div>
                <p class="leading-relaxed">{{ $question->featured_note }}</p>
            </div>
        @endif
    </div>

    <!-- Answers Thread -->
    <div class="space-y-4">
        <h3 class="text-base font-bold text-white flex items-center gap-2">
            <span>💬 Các phản hồi & giải đáp trước đó</span>
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
                            <p class="text-[11px] text-slate-500">{{ $ans->created_at->format('H:i d/m/Y') }}</p>
                        </div>
                    </div>
                </div>

                <div class="text-xs text-slate-200 leading-relaxed whitespace-pre-line pl-11">
                    {{ $ans->content }}
                </div>

                @if(!empty($ans->images) && is_array($ans->images))
                    <div class="pl-11 pt-1 grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach($ans->images as $ansImg)
                            <a href="{{ $ansImg }}" target="_blank" class="block rounded-lg overflow-hidden border border-slate-700 hover:border-emerald-500 transition group relative">
                                <img src="{{ $ansImg }}" alt="Ảnh đính kèm" class="w-full h-28 object-cover group-hover:scale-105 transition-transform duration-200">
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="p-6 text-center rounded-xl bg-slate-900 border border-slate-800 text-slate-400 text-xs">
                Chưa có câu trả lời nào cho câu hỏi này. Hãy gửi lời giải đáp bên dưới để học viên nhận được thông báo!
            </div>
        @endforelse
    </div>

    <!-- Instructor Answer Form -->
    <div class="rounded-2xl bg-slate-800/90 border border-emerald-500/30 p-6 shadow-xl space-y-5">
        <div class="pb-3 border-b border-slate-700/60">
            <h3 class="text-base font-bold text-emerald-400 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Soạn Lời Giải Đáp (Dành Cho Giảng Viên)
            </h3>
            <p class="text-xs text-slate-400 mt-1">
                Câu trả lời sẽ được gửi trực tiếp qua hệ thống thông báo cho học viên {{ $question->user->name ?? '' }}.
            </p>
        </div>

        <form method="POST" action="{{ route('admin.questions.answer', $question->id) }}" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <!-- Answer Content -->
            <div>
                <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Nội dung giải đáp chi tiết <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="content" 
                    id="content" 
                    rows="6" 
                    required 
                    placeholder="Nhập giải thích, phân tích kỹ thuật hoặc nhận xét về biểu đồ của học viên..." 
                    class="w-full px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 @error('content') border-rose-500 @enderror"
                >{{ old('content') }}</textarea>
                @error('content')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Image Attachments -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Đính kèm ảnh biểu đồ minh họa (Chart sửa lỗi / phân tích chuẩn)
                </label>
                <input 
                    type="file" 
                    name="images[]" 
                    multiple 
                    accept="image/*" 
                    class="block w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600"
                />
            </div>

            <!-- Featured Evaluation Toggle -->
            <div class="p-4 rounded-xl bg-slate-900 border border-slate-700/80 space-y-3" x-data="{ isFeatured: {{ old('is_featured', $question->is_featured ? 'true' : 'false') }} }">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" 
                           name="is_featured" 
                           value="1" 
                           x-model="isFeatured"
                           class="mt-0.5 rounded bg-slate-800 border-slate-600 text-amber-500 focus:ring-amber-500 w-4 h-4">
                    <div>
                        <span class="text-xs font-bold text-amber-400 flex items-center gap-1.5">
                            ⭐ Đánh giá câu hỏi này là CÂU HỎI TIÊU BIỂU
                        </span>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Câu hỏi và lời giải đáp này sẽ được đưa vào Showcase của khóa học để các học viên khác cùng đọc và học tập.
                        </p>
                    </div>
                </label>

                <div x-show="isFeatured" x-collapse class="pt-2 border-t border-slate-800">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Ghi chú lý do tiêu biểu (hiển thị cho toàn bộ học viên)</label>
                    <textarea name="featured_note" 
                              rows="2" 
                              placeholder="Ví dụ: Câu hỏi rất hay về việc phân biệt FVG thật và bẫy Fakeout trong khung M15." 
                              class="w-full px-3 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs placeholder-slate-500 focus:outline-none focus:border-amber-500">{{ old('featured_note', $question->featured_note) }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-3 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                    Gửi Lời Giải Đáp & Cập Nhật Trạng Thái
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

