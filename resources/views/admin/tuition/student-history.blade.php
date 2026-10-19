@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.tuition.index') }}" class="text-xs text-sky-400 hover:text-sky-300">
                    ← Quay lại quản lý học phí
                </a>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2 mt-1">
                <span>Lịch Sử Đóng Tiền: {{ $student->name }}</span>
            </h1>
            <p class="text-sm text-slate-400 mt-0.5">
                Email: {{ $student->email }} @if($student->phone) • SĐT: {{ $student->phone }} @endif
            </p>
        </div>

        <div class="flex items-center gap-3">
            @php
                $tuition = $student->tuition_status;
            @endphp
            @if($tuition['is_overdue'] || $tuition['code'] === 'due_soon')
                <form method="POST" action="{{ route('admin.tuition.remind', $student->id) }}" onsubmit="return confirm('Bạn có chắc muốn gửi thông báo nhắc học phí đến học viên {{ $student->name }}?')">
                    @csrf
                    <button type="submit" 
                            class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span>Nhắc đóng phí</span>
                    </button>
                </form>
            @endif

            <a href="{{ route('admin.tuition.create', ['student_id' => $student->id]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Ghi nhận thu tiền</span>
            </a>
        </div>
    </div>

    <!-- Student Summary Card -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 block mb-1">Trạng thái học phí</span>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold {{ $tuition['badge_class'] }}">
                    {{ $tuition['label'] }}
                </span>
            </div>
            <span class="text-[11px] text-slate-500 block mt-1">{{ $tuition['description'] }}</span>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 block mb-1">Hạn dùng đến ngày</span>
            <div class="text-lg font-bold font-mono {{ $tuition['is_overdue'] ? 'text-rose-400' : 'text-slate-100' }}">
                @if($student->billing_type === 'full_course')
                    Trọn đời
                @elseif($student->paid_until)
                    {{ $student->paid_until->format('d/m/Y') }}
                @else
                    Chưa có
                @endif
            </div>
            <span class="text-[11px] text-slate-500 block mt-1">
                {{ $student->paid_until ? $student->paid_until->diffForHumans() : 'Chưa ghi nhận' }}
            </span>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 block mb-1">Mức học phí / tháng</span>
            <div class="text-lg font-bold font-mono text-emerald-400">
                {{ number_format($student->monthly_tuition_fee, 0, ',', '.') }}đ
            </div>
            <span class="text-[11px] text-slate-500 block mt-1">Hình thức: {{ $student->billing_type }}</span>
        </div>

        <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-xs text-slate-400 block mb-1">Tổng tiền đã đóng</span>
            <div class="text-lg font-bold font-mono text-white">
                {{ number_format($totalPaid, 0, ',', '.') }}đ
            </div>
            <span class="text-[11px] text-slate-500 block mt-1">{{ $payments->total() }} lần đóng</span>
        </div>
    </div>

    <!-- Payments Ledger Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 bg-slate-900/60 border-b border-slate-700/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>📜 Sổ cái các lần nộp học phí</span>
            </h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/40 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Ngày thu</th>
                        <th class="py-3.5 px-4">Số tiền</th>
                        <th class="py-3.5 px-4">Kỳ hạn nộp</th>
                        <th class="py-3.5 px-4">Hình thức & Phương thức</th>
                        <th class="py-3.5 px-4 text-center">Ảnh bill</th>
                        <th class="py-3.5 px-4">Ghi chú & Người thu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-750/50 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-xs text-slate-300 font-mono">
                                {{ $p->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="text-sm font-bold font-mono text-emerald-400">
                                    +{{ number_format($p->amount, 0, ',', '.') }}đ
                                </span>
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($p->billing_type === 'full_course')
                                    <span class="px-2 py-0.5 rounded text-xs bg-indigo-500/10 text-indigo-400 border border-indigo-500/30">
                                        💎 Trọn gói cả khóa
                                    </span>
                                @elseif($p->start_date && $p->end_date)
                                    <div class="text-xs font-mono text-slate-200">
                                        {{ $p->start_date->format('d/m/Y') }} → {{ $p->end_date->format('d/m/Y') }}
                                    </div>
                                    <span class="text-[11px] text-slate-400 block">({{ $p->months_count }} tháng)</span>
                                @else
                                    <span class="text-xs text-slate-500">--</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 whitespace-nowrap text-xs">
                                <span class="text-slate-300 block">{{ $p->billing_type_label }}</span>
                                <span class="text-slate-500">{{ $p->payment_method_label }}</span>
                                @if($p->transaction_code)
                                    <span class="text-[10px] font-mono text-sky-400 block">Ref: {{ $p->transaction_code }}</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($p->payment_proof)
                                    <a href="{{ $p->payment_proof }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-sky-400 hover:text-sky-300 border border-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Xem bill</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-500">--</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-xs text-slate-400">
                                <div>{{ $p->note ?: 'Không có ghi chú' }}</div>
                                <div class="text-[11px] text-slate-500 mt-0.5">Thu bởi: {{ $p->admin?->name ?? 'Admin' }}</div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Học viên này chưa có lịch sử đóng học phí nào.
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

