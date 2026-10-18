@extends('layouts.student')

@section('content')
@php
    $submission = $mySubmission ?? ($submission ?? null);
    $latestReview = $submission ? $submission->reviews->last() : null;
@endphp
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('student.assignments.index') }}" class="hover:text-white">Bài tập & Bài nộp</a>
                <span>/</span>
                <span class="text-sky-400">Chi tiết bài tập</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white">{{ $assignment->title }}</h1>
            <p class="text-sm text-slate-400">
                Khóa: {{ $assignment->lesson->course->title ?? 'Khóa học' }} • Bài: {{ $assignment->lesson->title ?? 'Bài học' }}
            </p>
        </div>

        <a href="{{ route('student.assignments.index') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại danh sách bài tập
        </a>
    </div>

    <!-- Assignment Description & Requirements Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-4">
        <div>
            <h4 class="text-xs font-semibold uppercase text-slate-400 tracking-wider mb-1">Đề bài yêu cầu</h4>
            <p class="text-sm text-slate-200 leading-relaxed whitespace-pre-line">{{ $assignment->description }}</p>
        </div>

        @if(!empty($assignment->requirements))
            <div class="pt-3 border-t border-slate-700/60">
                <h4 class="text-xs font-semibold uppercase text-slate-400 tracking-wider mb-1">Tiêu chí chấm điểm & Yêu cầu</h4>
                <div class="p-3.5 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 font-mono whitespace-pre-line leading-relaxed">{{ $assignment->requirements }}</div>
            </div>
        @endif

        <div class="pt-3 border-t border-slate-700/60 flex items-center gap-6 text-xs text-slate-400">
            <div>Hình thức: <span class="text-sky-400 font-semibold">{{ ucfirst($assignment->type ?? 'Phân tích') }}</span></div>
            <div>Hạn nộp: <span class="text-amber-400 font-mono font-medium">{{ $assignment->due_date ? \Carbon\Carbon::parse($assignment->due_date)->format('d/m/Y H:i') : 'Không giới hạn' }}</span></div>
        </div>
    </div>

    <!-- Submission & Teacher Review (If already submitted) -->
    @if($submission)
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-5">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700/60">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span class="text-emerald-400">📋</span>
                    Bài làm của bạn đã nộp
                </h3>
                <div>
                    @if($submission->status === 'pending')
                        <span class="px-2.5 py-1 rounded text-xs font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/30">
                            Đang chờ Giảng viên chấm điểm
                        </span>
                    @elseif(in_array($submission->status, ['reviewed', 'approved']))
                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                            ✓ Đã chấm: {{ number_format($submission->score ?? ($latestReview->score ?? 10), 1) }}/10
                        </span>
                    @elseif($submission->status === 'revision_needed')
                        <span class="px-2.5 py-1 rounded text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                            ⚠️ Giảng viên yêu cầu sửa lại
                        </span>
                    @endif
                </div>
            </div>

            <!-- Student Submitted Content -->
            <div>
                <span class="text-xs text-slate-400 block mb-1">Nội dung bài làm:</span>
                <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-200 whitespace-pre-line leading-relaxed">
                    {{ $submission->content }}
                </div>
            </div>

            <!-- Attached File / Image -->
            @if(!empty($submission->attachment_path))
                <div>
                    <span class="text-xs text-slate-400 block mb-1.5">Ảnh biểu đồ / Tệp đính kèm:</span>
                    @php
                        $isImg = preg_match('/\.(jpg|jpeg|png|webp|svg)$/i', $submission->attachment_path);
                    @endphp
                    @if($isImg)
                        <img src="{{ asset('storage/' . $submission->attachment_path) }}" alt="Chart screenshot" class="max-h-80 rounded-lg border border-slate-700 shadow-md">
                    @else
                        <a href="{{ asset('storage/' . $submission->attachment_path) }}" target="_blank" download class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg bg-slate-900 border border-slate-700 text-sky-400 text-xs font-semibold hover:border-sky-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Tải file bài nộp về máy
                        </a>
                    @endif
                </div>
            @endif

            <!-- Review Feedback Box -->
            @if($latestReview || $submission->score !== null)
                <div class="p-4 rounded-xl bg-slate-950 border border-emerald-500/40 space-y-3">
                    <div class="flex items-center justify-between pb-2 border-b border-slate-800">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Nhận xét từ Giảng viên ({{ $latestReview->reviewer->name ?? 'Giảng viên' }})
                        </span>
                        <span class="text-base font-bold font-mono text-emerald-400">
                            Điểm: {{ number_format($latestReview->score ?? $submission->score, 1) }}/10
                        </span>
                    </div>

                    @if(!empty($latestReview->feedback))
                        <p class="text-sm text-slate-200 leading-relaxed whitespace-pre-line">
                            "{{ $latestReview->feedback }}"
                        </p>
                    @endif

                    @if(!empty($latestReview->corrections))
                        <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-xs text-rose-300">
                            <strong class="block mb-1 text-rose-400">Điểm cần sửa và rút kinh nghiệm:</strong>
                            <p class="whitespace-pre-line">{{ $latestReview->corrections }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <!-- Submission Form (For New Submission or Resubmission) -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <h3 class="text-base font-bold text-white mb-2 flex items-center gap-2">
            <span>{{ $submission ? '✍️ Sửa Lại & Nộp Bài Mới' : '✍️ Biểu Mẫu Nộp Bài Làm' }}</span>
        </h3>
        <p class="text-xs text-slate-400 mb-5">
            {{ $submission ? 'Bạn có thể chỉnh sửa lại nội dung bài làm và tải ảnh chart mới để giảng viên chấm lại.' : 'Điền câu trả lời chi tiết và đính kèm ảnh biểu đồ TradingView của bạn.' }}
        </p>

        <form method="POST" action="{{ route('student.assignments.submit', $assignment->id) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Text Content -->
            <div>
                <label for="content" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Nội dung phân tích & Câu trả lời bài tập <span class="text-rose-400">*</span>
                </label>
                <textarea 
                    name="content" 
                    id="content" 
                    rows="6" 
                    required 
                    placeholder="Trình bày chi tiết lý do vào lệnh, vùng cản bạn nhận diện, các bước quản trị rủi ro..." 
                    class="w-full px-4 py-3 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-sm focus:outline-none focus:border-sky-500 focus:ring-1 focus:ring-sky-500 @error('content') border-rose-500 @enderror"
                >{{ old('content', $submission->content ?? '') }}</textarea>
                @error('content')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- File Upload -->
            <div>
                <label for="attachment" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Ảnh chụp biểu đồ phân tích (TradingView Chart) hoặc file bài làm
                </label>
                <input 
                    type="file" 
                    name="attachment" 
                    id="attachment" 
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-sky-400 hover:file:bg-slate-600 cursor-pointer"
                />
                <p class="text-[11px] text-slate-500 mt-1">Hỗ trợ ảnh PNG, JPG hoặc file tài liệu PDF, DOCX (tối đa 10MB)</p>
                @error('attachment')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Link Chart -->
            <div>
                <label for="chart_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Hoặc đường dẫn TradingView Chart Link (Tùy chọn)
                </label>
                <input 
                    type="url" 
                    name="chart_url" 
                    id="chart_url" 
                    value="{{ old('chart_url', $submission->chart_url ?? '') }}" 
                    placeholder="https://www.tradingview.com/x/..." 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 text-xs font-mono focus:outline-none focus:border-sky-500"
                />
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ route('student.assignments.index') }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ $submission ? 'Cập nhật & Nộp lại' : 'Nộp bài làm ngay' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
