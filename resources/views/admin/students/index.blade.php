@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white">Quản Lý Học Viên</h1>
            <p class="text-sm text-slate-400 mt-1">Danh sách tất cả học viên Forex trong học viện MinhTrading</p>
        </div>
        <a href="{{ Route::has('admin.students.create') ? route('admin.students.create') : url('/admin/students/create') }}" 
           class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-sm font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
            Thêm học viên mới
        </a>
    </div>

    <!-- Search & Filter Bar -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-4 shadow-lg">
        <form method="GET" action="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="relative sm:col-span-2">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-500">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Tìm kiếm theo tên, email, số điện thoại..." 
                    class="w-full pl-10 pr-4 py-2 text-sm rounded-lg bg-slate-900/80 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors"
                />
            </div>
            
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-sm font-medium transition-colors">
                    Tìm kiếm
                </button>
                @if(request('search'))
                    <a href="{{ Route::has('admin.students.index') ? route('admin.students.index') : url('/admin/students') }}" class="px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 text-sm transition-colors" title="Xóa bộ lọc">
                        ✕
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-900/60 text-xs font-semibold text-slate-400 uppercase tracking-wider border-b border-slate-700">
                        <th class="py-3.5 px-4">Học viên</th>
                        <th class="py-3.5 px-4">Liên hệ</th>
                        <th class="py-3.5 px-4">Trạng thái</th>
                        <th class="py-3.5 px-4 text-center">Tổng lệnh</th>
                        <th class="py-3.5 px-4 text-center">Tỷ lệ thắng</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($students ?? [] as $student)
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-700 text-sky-400 font-bold flex items-center justify-center text-sm border border-slate-600">
                                        {{ strtoupper(substr($student->name ?? 'HV', 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ Route::has('admin.students.show') ? route('admin.students.show', $student->id) : url('/admin/students/' . $student->id) }}" class="font-semibold text-white hover:text-emerald-400 transition-colors">
                                            {{ $student->name }}
                                        </a>
                                        <p class="text-xs text-slate-400">Tham gia: {{ isset($student->created_at) ? $student->created_at->format('d/m/Y') : '12/01/2026' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <p class="text-slate-200">{{ $student->email }}</p>
                                <p class="text-xs text-slate-400">{{ $student->phone ?? 'Chưa cập nhật' }}</p>
                            </td>
                            <td class="py-3.5 px-4">
                                <x-status-badge :status="($student->is_active ?? true) ? 'active' : 'inactive'" :label="($student->is_active ?? true) ? 'Đang học' : 'Tạm khóa'" />
                            </td>
                            <td class="py-3.5 px-4 text-center font-mono font-semibold text-white">
                                {{ $student->trades_count ?? $student->trades()->count() ?? 0 }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                @php
                                    $winRate = $student->win_rate ?? 68;
                                @endphp
                                <span class="font-mono font-bold text-sm {{ $winRate >= 50 ? 'text-emerald-400' : 'text-rose-400' }}">
                                    {{ $winRate }}%
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ Route::has('admin.students.show') ? route('admin.students.show', $student->id) : url('/admin/students/' . $student->id) }}" 
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-sky-400 hover:bg-slate-700/60" title="Chi tiết học viên">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    </a>
                                    <a href="{{ Route::has('admin.students.stats') ? route('admin.students.stats', $student->id) : url('/admin/students/' . $student->id . '/stats') }}" 
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-400 hover:bg-slate-700/60" title="Thống kê trading">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                    </a>
                                    <a href="{{ Route::has('admin.students.edit') ? route('admin.students.edit', $student->id) : url('/admin/students/' . $student->id . '/edit') }}" 
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-amber-400 hover:bg-slate-700/60" title="Chỉnh sửa">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    </a>
                                    <form method="POST" action="{{ Route::has('admin.students.destroy') ? route('admin.students.destroy', $student->id) : url('/admin/students/' . $student->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn xóa học viên này?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-slate-700/60" title="Xóa">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                <p class="text-base font-medium text-slate-300">Chưa có học viên nào phù hợp</p>
                                <p class="text-xs text-slate-500 mt-1">Hãy thêm học viên mới hoặc thay đổi từ khóa tìm kiếm</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($students) && method_exists($students, 'links'))
            <div class="p-4 border-t border-slate-700">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
