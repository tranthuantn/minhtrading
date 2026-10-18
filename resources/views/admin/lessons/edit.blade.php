@extends('layouts.admin')

@section('styles')
<!-- Quill.js Snow Theme -->
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet" />
<style>
    /* Dark theme styling for Quill Editor */
    .ql-toolbar.ql-snow {
        background-color: #0f172a;
        border-color: #334155 !important;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    .ql-container.ql-snow {
        background-color: #020617;
        border-color: #334155 !important;
        border-bottom-left-radius: 0.5rem;
        border-bottom-right-radius: 0.5rem;
        color: #f1f5f9;
        font-family: inherit;
        font-size: 0.95rem;
        min-height: 280px;
    }
    .ql-snow .ql-stroke {
        stroke: #94a3b8;
    }
    .ql-snow .ql-fill {
        fill: #94a3b8;
    }
    .ql-snow .ql-picker {
        color: #94a3b8;
    }
    .ql-snow .ql-picker-options {
        background-color: #0f172a;
        border-color: #334155 !important;
    }
    .ql-snow .ql-picker-item:hover, .ql-snow .ql-picker-label:hover {
        color: #34d399 !important;
    }
    .ql-snow .ql-picker-item:hover .ql-stroke, .ql-snow .ql-picker-label:hover .ql-stroke {
        stroke: #34d399 !important;
    }
    .ql-editor.ql-blank::before {
        color: #64748b;
        font-style: normal;
    }
    .ql-editor img {
        border-radius: 0.5rem;
        margin: 1rem 0;
        max-width: 100%;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.3);
        border: 1px solid #334155;
    }
</style>
@endsection

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ route('admin.courses.index') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <a href="{{ route('admin.courses.show', $lesson->course_id) }}" class="hover:text-white">{{ $lesson->course->title ?? 'Chi tiết' }}</a>
                <span>/</span>
                <span class="text-emerald-400">Chỉnh sửa bài học</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>Chỉnh Sửa Bài Học</span>
            </h1>
            <p class="text-sm text-slate-400">{{ $lesson->title }}</p>
        </div>
        <a href="{{ route('admin.courses.show', $lesson->course_id) }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại khóa học
        </a>
    </div>

    <!-- Form -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl">
        <form id="lesson-form" method="POST" action="{{ route('admin.courses.lessons.update', [$lesson->course_id, $lesson->id]) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Course Selection -->
            <div>
                <label for="course_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Thuộc khóa học <span class="text-rose-400">*</span>
                </label>
                <select 
                    name="course_id" 
                    id="course_id" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm"
                >
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ (old('course_id', $lesson->course_id) == $c->id) ? 'selected' : '' }}>
                            {{ $c->title }}
                        </option>
                    @endforeach
                </select>
                @error('course_id')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Title -->
            <div>
                <label for="title" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tiêu đề bài học <span class="text-rose-400">*</span>
                </label>
                <input 
                    type="text" 
                    name="title" 
                    id="title" 
                    value="{{ old('title', $lesson->title) }}" 
                    required 
                    class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm @error('title') border-rose-500 @enderror"
                />
                @error('title')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Video Link -->
            <div>
                <label for="video_url" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Đường dẫn Link Video bài giảng (YouTube, Vimeo, Google Drive, Loom...)
                </label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <input 
                        type="text" 
                        name="video_url" 
                        id="video_url" 
                        value="{{ old('video_url', $lesson->video_url) }}" 
                        placeholder="https://drive.google.com/file/d/... hoặc https://www.youtube.com/watch?v=..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm font-mono @error('video_url') border-rose-500 @enderror"
                    />
                </div>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    💡 Hỗ trợ: <strong>YouTube</strong>, <strong>Google Drive</strong>, <strong>Vimeo</strong>, <strong>Loom</strong> hoặc mã nhúng <code>&lt;iframe&gt;</code>. Với Google Drive, hệ thống tự động chuyển link sang định dạng nhúng video (lưu ý chia sẻ quyền file: <em>"Bất kỳ ai có đường liên kết đều có thể xem"</em>).
                </p>
                @error('video_url')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Rich Text Content Editor with Images -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300">
                        Nội dung bài viết & Hình ảnh minh họa (Soạn thảo văn bản) <span class="text-rose-400">*</span>
                    </label>
                    <span class="text-[11px] text-emerald-400 font-medium">Hỗ trợ định dạng chữ, chèn ảnh, bảng, link & danh sách</span>
                </div>

                <!-- Quill Toolbar & Editor Container -->
                <div id="quill-toolbar">
                    <span class="ql-formats">
                        <select class="ql-header">
                            <option value="1">Tiêu đề lớn H1</option>
                            <option value="2">Tiêu đề vừa H2</option>
                            <option value="3">Tiêu đề nhỏ H3</option>
                            <option selected>Văn bản thường</option>
                        </select>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-bold" title="In đậm (Ctrl+B)"></button>
                        <button class="ql-italic" title="In nghiêng (Ctrl+I)"></button>
                        <button class="ql-underline" title="Gạch chân (Ctrl+U)"></button>
                        <button class="ql-strike" title="Gạch ngang"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-blockquote" title="Trích dẫn"></button>
                        <button class="ql-code-block" title="Khối mã lệnh"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-list" value="ordered" title="Danh sách số"></button>
                        <button class="ql-list" value="bullet" title="Danh sách chấm"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-link" title="Chèn liên kết"></button>
                        <button class="ql-image" title="Chèn hình ảnh từ máy tính"></button>
                    </span>
                    <span class="ql-formats">
                        <button class="ql-clean" title="Xóa định dạng"></button>
                    </span>
                </div>

                <div id="quill-editor">{!! old('content', $lesson->content) !!}</div>

                <!-- Hidden input to submit with the form -->
                <input type="hidden" name="content" id="content-input" value="{{ old('content', $lesson->content) }}" required>

                @error('content')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Attachment File -->
            <div>
                <label for="attachment" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                    Tài liệu đính kèm (PDF, Word, Excel, Slide bài giảng, Cheat-sheet)
                </label>

                @if(!empty($lesson->attachments) && count($lesson->attachments) > 0)
                    <div class="mb-3 space-y-1.5 p-3 rounded-lg bg-slate-900 border border-slate-700">
                        <span class="text-xs text-slate-400 font-semibold block mb-1">Các file đang có:</span>
                        @foreach($lesson->attachments as $att)
                            <div class="flex items-center justify-between text-xs text-slate-300 py-1">
                                <span class="flex items-center gap-2">
                                    <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    {{ $att['name'] ?? 'File đính kèm' }}
                                </span>
                                <a href="{{ asset('storage/' . ($att['url'] ?? '')) }}" target="_blank" class="text-sky-400 hover:underline text-[11px]">
                                    Xem file
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif

                <input 
                    type="file" 
                    name="attachment" 
                    id="attachment" 
                    class="w-full text-sm text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600 cursor-pointer"
                />
                <p class="text-[11px] text-slate-500 mt-1">Tải tệp mới lên nếu muốn bổ sung hoặc thay thế</p>
                @error('attachment')
                    <p class="mt-1 text-xs text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bài Tập Thực Hành Tương Ứng (Đồng bộ) -->
            @php
                $currentAssignment = $lesson->assignments->first() ?? null;
            @endphp
            <div class="p-4 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div>
                        <h3 class="text-sm font-bold text-white flex items-center gap-2">
                            <span>📝 Bài Tập Thực Hành Tương Ứng</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Đồng bộ theo bài học</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Mỗi bài học tương ứng với 1 bài tập. Học viên sẽ nộp bài thực hành theo đề bài này</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="assignment_title" class="block text-xs font-semibold text-slate-300 mb-1">
                            Tiêu đề bài tập
                        </label>
                        <input 
                            type="text" 
                            name="assignment_title" 
                            id="assignment_title" 
                            value="{{ old('assignment_title', $currentAssignment->title ?? ('Bài tập: ' . $lesson->title)) }}" 
                            class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500"
                        />
                    </div>

                    <div>
                        <label for="assignment_type" class="block text-xs font-semibold text-slate-300 mb-1">
                            Loại bài tập thực hành
                        </label>
                        <select name="assignment_type" id="assignment_type" class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500">
                            <option value="analysis" {{ old('assignment_type', $currentAssignment->type ?? 'analysis') === 'analysis' ? 'selected' : '' }}>🔍 Phân tích biểu đồ (Chart Analysis)</option>
                            <option value="backtest" {{ old('assignment_type', $currentAssignment->type ?? '') === 'backtest' ? 'selected' : '' }}>📊 Nhật ký Backtest</option>
                            <option value="trade_entry" {{ old('assignment_type', $currentAssignment->type ?? '') === 'trade_entry' ? 'selected' : '' }}>💹 Kế hoạch vào lệnh (Live/Demo)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="assignment_requirements" class="block text-xs font-semibold text-slate-300 mb-1">
                            Yêu cầu thực hành
                        </label>
                        <textarea 
                            name="assignment_requirements" 
                            id="assignment_requirements" 
                            rows="2" 
                            class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white placeholder-slate-500 text-xs focus:outline-none focus:border-emerald-500"
                        >{{ old('assignment_requirements', $currentAssignment->requirements ?? '') }}</textarea>
                    </div>

                    <div>
                        <label for="assignment_due_date" class="block text-xs font-semibold text-slate-300 mb-1">
                            Hạn nộp bài (Deadline)
                        </label>
                        <input 
                            type="datetime-local" 
                            name="assignment_due_date" 
                            id="assignment_due_date" 
                            value="{{ old('assignment_due_date', isset($currentAssignment->due_date) ? \Carbon\Carbon::parse($currentAssignment->due_date)->format('Y-m-d\TH:i') : now()->addDays(7)->format('Y-m-d\TH:i')) }}" 
                            class="w-full px-3.5 py-2 rounded-lg bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500 font-mono"
                        />
                    </div>
                </div>
            </div>

            <!-- Sort Order & Published -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="sort_order" class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Thứ tự bài học (#)
                    </label>
                    <input 
                        type="number" 
                        name="sort_order" 
                        id="sort_order" 
                        value="{{ old('sort_order', $lesson->sort_order) }}" 
                        min="1" 
                        class="w-full px-4 py-2.5 rounded-lg bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 text-sm font-mono"
                    />
                </div>

                <div x-data="{ published: {{ old('is_published', $lesson->is_published) ? 'true' : 'false' }} }">
                    <label class="block text-xs font-semibold uppercase tracking-wider text-slate-300 mb-1.5">
                        Trạng thái hiển thị
                    </label>
                    <div class="flex items-center justify-between p-3 rounded-lg bg-slate-900 border border-slate-700">
                        <span class="text-xs text-slate-300" x-text="published ? 'Hiển thị cho học viên được cấp quyền' : 'Tạm ẩn (Bản nháp)'"></span>
                        <input type="hidden" name="is_published" :value="published ? 1 : 0">
                        <button 
                            type="button" 
                            @click="published = !published" 
                            :class="published ? 'bg-emerald-600' : 'bg-slate-700'" 
                            class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        >
                            <span :class="published ? 'translate-x-5' : 'translate-x-0'" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"></span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="pt-4 border-t border-slate-700/60 flex items-center justify-end gap-3">
                <a href="{{ route('admin.courses.show', $lesson->course_id) }}" 
                   class="px-5 py-2.5 rounded-lg text-sm font-medium text-slate-300 hover:text-white hover:bg-slate-700/60 transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    Cập nhật bài học
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quill = new Quill('#quill-editor', {
            modules: {
                toolbar: {
                    container: '#quill-toolbar',
                    handlers: {
                        image: imageHandler
                    }
                }
            },
            placeholder: 'Nhập nội dung bài giảng chi tiết, giải thích các khái niệm, quy tắc vào lệnh, dán hoặc tải ảnh biểu đồ trực tiếp vào đây...',
            theme: 'snow'
        });

        // Custom image upload handler for Quill
        function imageHandler() {
            const input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');
            input.click();

            input.onchange = async () => {
                const file = input.files[0];
                if (!file) return;

                const formData = new FormData();
                formData.append('image', file);
                formData.append('_token', '{{ csrf_token() }}');

                try {
                    const response = await fetch('{{ route("admin.lessons.upload-image") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();
                    if (data.success && data.url) {
                        const range = quill.getSelection(true);
                        quill.insertEmbed(range.index, 'image', data.url);
                        quill.setSelection(range.index + 1);
                    } else {
                        insertBase64Image(file);
                    }
                } catch (e) {
                    insertBase64Image(file);
                }
            };
        }

        function insertBase64Image(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const range = quill.getSelection(true);
                quill.insertEmbed(range.index, 'image', e.target.result);
                quill.setSelection(range.index + 1);
            };
            reader.readAsDataURL(file);
        }

        // Sync Quill HTML content to hidden input upon form submit
        const form = document.getElementById('lesson-form');
        const contentInput = document.getElementById('content-input');

        form.addEventListener('submit', function() {
            contentInput.value = quill.root.innerHTML;
        });
    });
</script>
@endsection
