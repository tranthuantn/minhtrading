@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.courses.index') ? route('admin.courses.index') : url('/admin/courses') }}" class="hover:text-white">Khóa học</a>
                <span>/</span>
                <span class="text-emerald-400">Chi tiết & Quản lý bài học</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                {{ $course->title }}
                <span class="text-xs px-2.5 py-0.5 rounded-full {{ $course->is_published ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700 text-slate-400' }}">
                    {{ $course->is_published ? 'Đang xuất bản' : 'Bản nháp' }}
                </span>
            </h1>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.courses.lessons.create', $course->id) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Thêm bài học mới
            </a>
            <a href="{{ route('admin.courses.edit', $course->id) }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Sửa thông tin
            </a>
        </div>
    </div>

    <!-- Course Overview Card -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Mô tả chương trình đào tạo</h3>
                <p class="text-sm text-slate-200 leading-relaxed">
                    {{ $course->description ?? 'Chưa có mô tả chi tiết cho khóa học này.' }}
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-700/60 flex flex-wrap items-center gap-6 text-xs text-slate-400">
                <div>Tổng số bài học: <span class="font-bold text-white font-mono">{{ $course->lessons ? count($course->lessons) : 0 }}</span></div>
                <div>Thứ tự hiển thị: <span class="font-bold text-white font-mono">#{{ $course->sort_order }}</span></div>
                <div>Học viên được cấp quyền: <span class="font-bold text-emerald-400 font-mono">{{ $course->enrolledStudents ? count($course->enrolledStudents) : 0 }} học viên</span></div>
            </div>
        </div>

        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg flex flex-col justify-between">
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-2">Hình ảnh đại diện</h3>
                @if(!empty($course->thumbnail))
                    <img src="{{ str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-32 object-cover rounded-lg border border-slate-700">
                @else
                    <div class="w-full h-32 rounded-lg bg-slate-900 border border-slate-700 flex items-center justify-center text-slate-500 text-xs">
                        Chưa có ảnh đại diện
                    </div>
                @endif
            </div>
            <div class="mt-3 text-right">
                <form method="POST" action="{{ route('admin.courses.toggle', $course->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs font-semibold {{ $course->is_published ? 'text-amber-400 hover:underline' : 'text-emerald-400 hover:underline' }}">
                        {{ $course->is_published ? 'Chuyển về Bản nháp' : 'Xuất bản khóa học' }} &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- PART 1: DANH SÁCH BÀI HỌC VÀ SẮP XẾP THỨ TỰ -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 bg-slate-900/60">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>📑 Danh Sách Bài Học & Sắp Xếp Thứ Tự</span>
                    <span class="text-xs font-semibold px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        {{ count($course->lessons ?? []) }} bài
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Dùng nút mũi tên hoặc chỉnh số thứ tự để sắp xếp bài học cho học viên theo đúng tiến trình</p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('admin.courses.lessons.create', $course->id) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition shadow">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Soạn bài học mới
                </a>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.courses.lessons.reorder', $course->id) }}">
            @csrf
            <div class="divide-y divide-slate-700/60">
                @forelse($course->lessons ?? [] as $index => $lesson)
                    <div class="p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 hover:bg-slate-700/30 transition-colors group">
                        <div class="flex items-start sm:items-center gap-3">
                            <!-- Hidden ID input for reorder -->
                            <input type="hidden" name="lessons[{{ $index }}][id]" value="{{ $lesson->id }}">

                            <!-- Sort order input & arrow controls -->
                            <div class="flex items-center gap-1.5">
                                <input 
                                    type="number" 
                                    name="lessons[{{ $index }}][sort_order]" 
                                    value="{{ $lesson->sort_order }}" 
                                    min="1" 
                                    class="w-14 px-2 py-1 rounded bg-slate-900 border border-slate-700 text-emerald-400 font-mono font-bold text-xs text-center focus:outline-none focus:border-emerald-500"
                                    title="Chỉnh số thứ tự bài học"
                                />
                            </div>

                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h4 class="text-sm font-semibold text-white group-hover:text-emerald-400 transition-colors">
                                        {{ $lesson->title }}
                                    </h4>
                                    <span class="text-[11px] px-2 py-0.5 rounded {{ $lesson->is_published ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700 text-slate-400' }}">
                                        {{ $lesson->is_published ? 'Đang hiển thị' : 'Tạm ẩn' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-4 mt-1 text-xs text-slate-400">
                                    @if(!empty($lesson->video_url))
                                        <span class="inline-flex items-center gap-1 text-sky-400 font-mono text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            Có Video bài giảng
                                        </span>
                                    @endif

                                    @if(!empty($lesson->attachments) && count($lesson->attachments) > 0)
                                        <span class="inline-flex items-center gap-1 text-amber-400 text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            {{ count($lesson->attachments) }} file đính kèm
                                        </span>
                                    @endif

                                    @if($lesson->assignments_count > 0)
                                        <span class="inline-flex items-center gap-1 text-purple-400 text-[11px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            {{ $lesson->assignments_count }} bài tập
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex items-center gap-1.5 self-end sm:self-center">
                            <!-- Toggle publish -->
                            <form method="POST" action="{{ route('admin.lessons.toggle', $lesson->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700/60" title="{{ $lesson->is_published ? 'Ẩn bài học' : 'Xuất bản bài học' }}">
                                    @if($lesson->is_published)
                                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    @else
                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                                    @endif
                                </button>
                            </form>

                            <!-- Edit -->
                            <a href="{{ route('admin.courses.lessons.edit', [$course->id, $lesson->id]) }}" 
                               class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-700/60" title="Chỉnh sửa bài học">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>

                            <!-- Delete -->
                            <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course->id, $lesson->id]) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa bài học này?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-700/60" title="Xóa">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center text-slate-500">
                        <svg class="w-12 h-12 mx-auto mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-sm font-semibold text-slate-400">Chưa có bài học nào trong khóa này</p>
                        <p class="text-xs text-slate-500 mt-1">Bấm nút "Soạn bài học mới" ở góc trên để thêm bài học đầu tiên</p>
                    </div>
                @endforelse
            </div>

            @if(isset($course->lessons) && count($course->lessons) > 0)
                <div class="p-4 bg-slate-900/80 border-t border-slate-700 flex items-center justify-between">
                    <span class="text-xs text-slate-400">Sau khi điều chỉnh số thứ tự, bấm Lưu để cập nhật cho toàn bộ học viên:</span>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold transition">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Lưu thứ tự các bài học
                    </button>
                </div>
            @endif
        </form>
    </div>

    <!-- PART 2: QUẢN LÝ CẤP QUYỀN HỌC VIÊN -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 border-b border-slate-700 bg-slate-900/60 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <span>👥 Phân Quyền Học Viên Được Học Khóa Này</span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                        {{ count($course->enrolledStudents ?? []) }} học viên có quyền
                    </span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Học viên chỉ có thể xem khóa học và bài giảng khi được Giảng viên cấp quyền tại đây</p>
            </div>
        </div>

        <div class="p-5 space-y-6">
            <!-- Add Student to Course Form -->
            @if(isset($availableStudents) && count($availableStudents) > 0)
                <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-700">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-300 mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        Cấp quyền cho học viên mới
                    </h4>
                    <form method="POST" action="{{ route('admin.courses.enroll', $course->id) }}" class="flex flex-col sm:flex-row gap-3">
                        @csrf
                        <div class="flex-1">
                            <select name="user_ids[]" required multiple class="w-full px-3 py-2 rounded-lg bg-slate-950 border border-slate-700 text-white text-xs focus:outline-none focus:border-emerald-500 min-h-[70px]">
                                @foreach($availableStudents as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} ({{ $st->email }})</option>
                                @endforeach
                            </select>
                            <span class="text-[11px] text-slate-500 mt-1 block">Giữ Ctrl (hoặc Cmd) để chọn nhiều học viên cùng lúc</span>
                        </div>
                        <div class="sm:self-start">
                            <button type="submit" class="w-full sm:w-auto px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold transition shadow-lg shadow-emerald-600/20 whitespace-nowrap">
                                Cấp quyền ngay &rarr;
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            <!-- Enrolled Students Table -->
            <div class="overflow-x-auto rounded-lg border border-slate-700/80">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-900 border-b border-slate-700 text-[11px] uppercase tracking-wider text-slate-400">
                            <th class="py-3 px-4">Họ tên học viên</th>
                            <th class="py-3 px-4">Email</th>
                            <th class="py-3 px-4">Ngày cấp quyền</th>
                            <th class="py-3 px-4 text-center">Trạng thái</th>
                            <th class="py-3 px-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/60">
                        @forelse($course->enrolledStudents ?? [] as $st)
                            <tr class="hover:bg-slate-700/20 transition-colors">
                                <td class="py-3 px-4 font-semibold text-white flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-slate-700 flex items-center justify-center text-[10px] text-emerald-400 font-bold uppercase">
                                        {{ substr($st->name, 0, 1) }}
                                    </span>
                                    <span>{{ $st->name }}</span>
                                </td>
                                <td class="py-3 px-4 text-slate-300 font-mono">{{ $st->email }}</td>
                                <td class="py-3 px-4 text-slate-400">
                                    {{ !empty($st->pivot?->enrolled_at) ? \Carbon\Carbon::parse($st->pivot->enrolled_at)->format('d/m/Y H:i') : 'Đã cấp' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/30">
                                        Được phép học
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <form method="POST" action="{{ route('admin.courses.unenroll', [$course->id, $st->id]) }}" onsubmit="return confirm('Bạn có chắc chắn muốn thu hồi quyền truy cập khóa học của học viên {{ $st->name }}?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1 rounded bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 border border-rose-500/30 text-[11px] font-semibold transition" title="Thu hồi quyền truy cập">
                                            Thu hồi quyền
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-500">
                                    Chưa có học viên nào được cấp quyền truy cập khóa học này.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
