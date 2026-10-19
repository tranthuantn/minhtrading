@extends('layouts.student')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>💳 Thông Tin Học Phí & Biên Lai Của Bạn</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Theo dõi thời hạn học phí, lịch sử các lần đã nộp và thông tin hướng dẫn chuyển khoản gia hạn.
            </p>
        </div>
    </div>

    <!-- Status Overview & Bank Account Guide -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status Card (Left 1 col) -->
        <div class="p-5 rounded-2xl bg-slate-800/90 border {{ $statusInfo['is_overdue'] ? 'border-rose-500/50 bg-gradient-to-b from-rose-950/20 to-slate-800/90' : 'border-slate-700/80' }} shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Trạng thái hiện tại</span>
                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $statusInfo['badge_class'] }}">
                    {{ $statusInfo['label'] }}
                </span>
            </div>

            <div class="space-y-3">
                <div>
                    <span class="text-xs text-slate-400 block">Thời hạn học phí đến hết ngày:</span>
                    <div class="text-2xl font-bold font-mono {{ $statusInfo['is_overdue'] ? 'text-rose-400' : 'text-emerald-400' }} mt-0.5">
                        @if($student->billing_type === 'full_course')
                            💎 Trọn gói cả khóa
                        @elseif($student->paid_until)
                            {{ $student->paid_until->format('d/m/Y') }}
                        @else
                            Chưa ghi nhận
                        @endif
                    </div>
                    <span class="text-xs text-slate-400 mt-1 block">
                        {{ $statusInfo['description'] }}
                    </span>
                </div>

                <div class="pt-3 border-t border-slate-700/60 flex items-center justify-between text-xs">
                    <span class="text-slate-400">Mức học phí niêm yết:</span>
                    <span class="font-bold font-mono text-white text-sm">
                        {{ number_format($student->monthly_tuition_fee, 0, ',', '.') }}đ/tháng
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <span class="text-slate-400">Tổng tiền đã đóng:</span>
                    <span class="font-bold font-mono text-emerald-400 text-sm">
                        {{ number_format($totalPaid, 0, ',', '.') }}đ
                    </span>
                </div>
            </div>

            @if($statusInfo['is_overdue'])
                <div class="p-3 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs">
                    ⚠️ Học phí của bạn đã quá hạn. Vui lòng chuyển khoản học phí duy trì theo thông tin bên phải để không bị gián đoạn quyền học tập.
                </div>
            @endif
        </div>

        <!-- Bank Transfer Instructions (Right 2 cols) -->
        <div class="lg:col-span-2 p-5 rounded-2xl bg-gradient-to-r from-slate-800 to-slate-850 border border-slate-700/80 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700/60 pb-3">
                <div class="flex items-center gap-2">
                    <span class="p-1.5 rounded-lg bg-sky-500/10 text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    </span>
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Thông tin chuyển khoản học phí</h3>
                </div>
                <span class="text-xs text-sky-400">Cập nhật tự động sau khi admin xác nhận</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-700/60 space-y-2">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Ngân hàng thụ hưởng:</span>
                        <span class="text-white font-bold text-sm">Techcombank (TCB)</span>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Số tài khoản:</span>
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-base font-bold text-sky-400">1903 8888 9999</span>
                        </div>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[11px]">Chủ tài khoản:</span>
                        <span class="text-white font-semibold">NGUYEN VAN MINH</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-900/80 border border-slate-700/60 space-y-2">
                    <div>
                        <span class="text-slate-400 block text-[11px]">Cú pháp chuyển khoản chuẩn:</span>
                        <div class="p-2 rounded bg-slate-950 font-mono text-emerald-400 font-bold text-xs border border-slate-800 select-all">
                            HOCPHI {{ strtoupper(Str::slug($student->name, '')) }} {{ $student->phone ?? 'SĐT' }}
                        </div>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">
                        Sau khi chuyển khoản, vui lòng chụp lại màn hình giao dịch và gửi cho Giảng viên hoặc Trợ giảng qua Zalo/Telegram để được kích hoạt ngay lập tức.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment Receipts History Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="p-4 bg-slate-900/60 border-b border-slate-700/50 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span>📜 Lịch sử biên lai đã thanh toán</span>
            </h3>
            <span class="text-xs text-slate-400">Tổng cộng {{ $payments->total() }} lần đóng</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/40 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50 font-semibold">
                    <tr>
                        <th class="py-3 px-4">Ngày đóng</th>
                        <th class="py-3 px-4">Số tiền</th>
                        <th class="py-3 px-4">Kỳ hạn học phí</th>
                        <th class="py-3 px-4">Hình thức & Phương thức</th>
                        <th class="py-3 px-4 text-center">Biên lai / Bill</th>
                        <th class="py-3 px-4">Ghi chú</th>
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
                            </td>

                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($p->payment_proof)
                                    <a href="{{ $p->payment_proof }}" target="_blank" 
                                       class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs bg-slate-900 text-sky-400 hover:text-sky-300 border border-slate-700 transition">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        <span>Xem bill</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-500">Đã xác nhận</span>
                                @endif
                            </td>

                            <td class="py-3 px-4 text-xs text-slate-400">
                                {{ $p->note ?: 'Học phí định kỳ' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Bạn chưa có biên lai học phí nào được ghi nhận.
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

