@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>📜 Lịch Sử Giao Dịch Thu Học Phí</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Toàn bộ các lần nộp tiền học phí của học viên đã được ghi nhận trong hệ thống.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tuition.index') }}" 
               class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">
                ← Bảng quản lý học phí
            </a>

            <a href="{{ route('admin.tuition.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Thu học phí mới</span>
            </a>
        </div>
    </div>

    <!-- Total Collected Banner -->
    <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </span>
            <div>
                <span class="text-xs text-slate-400">Tổng số tiền hiển thị theo danh sách lọc:</span>
                <div class="text-xl font-bold font-mono text-emerald-400">
                    {{ number_format($totalAmount, 0, ',', '.') }} VNĐ
                </div>
            </div>
        </div>
        <span class="text-xs text-slate-400">
            Tổng cộng <strong>{{ $payments->total() }}</strong> giao dịch
        </span>
    </div>

    <!-- Filter Form -->
    <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg">
        <form method="GET" action="{{ route('admin.tuition.history') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <select name="payment_method" onchange="this.form.submit()"
                        class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả phương thức --</option>
                    <option value="bank_transfer" {{ request('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản</option>
                    <option value="cash" {{ request('payment_method') === 'cash' ? 'selected' : '' }}>Tiền mặt</option>
                    <option value="crypto" {{ request('payment_method') === 'crypto' ? 'selected' : '' }}>Crypto / USDT</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Tên học viên, mã GD..."
                           class="w-full pl-8 pr-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-white transition">
                    Lọc
                </button>

                @if(request('payment_method') || request('search') || request('student_id'))
                    <a href="{{ route('admin.tuition.history') }}" class="text-xs text-sky-400 hover:text-sky-300">
                        ✕ Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Học viên</th>
                        <th class="py-3.5 px-4">Số tiền</th>
                        <th class="py-3.5 px-4">Kỳ hạn nộp</th>
                        <th class="py-3.5 px-4">Phương thức</th>
                        <th class="py-3.5 px-4 text-center">Bill biên lai</th>
                        <th class="py-3.5 px-4">Người thu & Ngày</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-750/50 transition">
                            <!-- Student -->
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.tuition.student', $p->user_id) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                    {{ $p->student?->name ?? 'N/A' }}
                                </a>
                                <span class="text-xs text-slate-400">{{ $p->student?->email }}</span>
                                @if($p->course)
                                    <span class="block text-[11px] text-sky-400 mt-0.5">Khóa: {{ $p->course->title }}</span>
                                @endif
                            </td>

                            <!-- Amount -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="text-sm font-bold font-mono text-emerald-400">
                                    {{ number_format($p->amount, 0, ',', '.') }}đ
                                </span>
                                <span class="text-[11px] text-slate-400 block">
                                    {{ $p->billing_type_label }}
                                </span>
                            </td>

                            <!-- Period -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($p->billing_type === 'full_course')
                                    <span class="px-2 py-0.5 rounded text-xs bg-indigo-500/10 text-indigo-400 border border-indigo-500/30 font-medium">
                                        💎 Trọn gói cả khóa
                                    </span>
                                @elseif($p->start_date && $p->end_date)
                                    <div class="text-xs font-mono text-slate-200">
                                        {{ $p->start_date->format('d/m/Y') }} → {{ $p->end_date->format('d/m/Y') }}
                                    </div>
                                    <span class="text-[11px] text-slate-400 block">
                                        ({{ $p->months_count }} tháng)
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500">--</span>
                                @endif
                                @if($p->note)
                                    <p class="text-[11px] text-slate-400 italic mt-0.5 max-w-xs truncate">{{ $p->note }}</p>
                                @endif
                            </td>

                            <!-- Method & Ref code -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="text-xs text-slate-300 block">{{ $p->payment_method_label }}</span>
                                @if($p->transaction_code)
                                    <span class="text-[11px] font-mono text-slate-400">Ref: {{ $p->transaction_code }}</span>
                                @endif
                            </td>

                            <!-- Proof bill -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($p->payment_proof)
                                    <a href="{{ $p->payment_proof }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-sky-400 hover:text-sky-300 border border-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Xem bill</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-500">Không có</span>
                                @endif
                            </td>

                            <!-- Admin & Date -->
                            <td class="py-3 px-4 whitespace-nowrap text-xs text-slate-400">
                                <div>Thu bởi: <strong class="text-slate-200">{{ $p->admin?->name ?? 'Admin' }}</strong></div>
                                <div>{{ $p->created_at->format('d/m/Y H:i') }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Chưa có giao dịch nào phù hợp.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
            <div class="p-4 border-t border-slate-700/50">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

