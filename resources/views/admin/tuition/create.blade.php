@extends('layouts.admin')

@section('content')
@php
    $studentsList = $students->map(function ($s) {
        return [
            'id' => $s->id,
            'name' => $s->name,
            'email' => $s->email,
            'phone' => $s->phone ?? '',
            'billing_type' => $s->billing_type ?? 'monthly',
            'monthly_fee' => (float) ($s->monthly_tuition_fee ?? 2000000),
            'paid_until' => $s->paid_until ? $s->paid_until->format('Y-m-d') : null,
            'paid_until_formatted' => $s->paid_until ? $s->paid_until->format('d/m/Y') : 'Chưa có',
            'status_label' => $s->tuition_status['label'] ?? '',
            'status_badge' => $s->tuition_status['badge_class'] ?? '',
            'is_overdue' => $s->tuition_status['is_overdue'] ?? false,
        ];
    });
@endphp

<script>
    function tuitionRecordForm() {
        return {
            students: @json($studentsList),
            selectedStudentId: '{{ old('user_id', $selectedStudentId ?? '') }}',
            billingType: '{{ old('billing_type', 'monthly') }}',
            monthsCount: {{ (int) old('months_count', 1) }},
            startDate: '{{ old('start_date', '') }}',
            endDate: '{{ old('end_date', '') }}',
            amount: '{{ old('amount', '') }}',
            note: '{{ old('note', '') }}',
            activePreset: '1m',

            get currentStudent() {
                if (!this.selectedStudentId) return null;
                return this.students.find(s => s.id == this.selectedStudentId) || null;
            },

            init() {
                if (this.selectedStudentId) {
                    this.onStudentChanged(false);
                }
            },

            onStudentChanged(resetPreset = true) {
                const st = this.currentStudent;
                if (!st) return;

                if (resetPreset && !this.amount) {
                    this.applyPreset('1m', 1);
                }
            },

            applyPreset(presetKey, months) {
                this.activePreset = presetKey;
                const st = this.currentStudent;
                const monthlyRate = st ? st.monthly_fee : 2000000;

                // Xác định ngày bắt đầu: Nếu đã có hạn cũ và hạn cũ còn trong tương lai -> bắt đầu từ ngày sau hạn cũ, ngược lại bắt đầu từ hôm nay
                let start = new Date();
                if (st && st.paid_until) {
                    const prevEnd = new Date(st.paid_until);
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    prevEnd.setHours(0, 0, 0, 0);

                    if (prevEnd >= today) {
                        start = new Date(prevEnd);
                        start.setDate(start.getDate() + 1);
                    }
                }

                if (presetKey === 'full_course') {
                    this.billingType = 'full_course';
                    this.monthsCount = 1;
                    this.startDate = this.formatDate(start);
                    this.endDate = '';
                    this.amount = 15000000;
                    this.note = 'Đóng học phí trọn gói cả khóa (học trọn đời)';
                    return;
                }

                if (presetKey === 'custom') {
                    this.billingType = 'custom';
                    return;
                }

                this.billingType = months > 1 ? 'multi_month' : 'monthly';
                this.monthsCount = months;

                // Tính ngày kết thúc: Start date + months tháng - 1 ngày
                const end = new Date(start);
                end.setMonth(end.getMonth() + months);
                end.setDate(end.getDate() - 1);

                this.startDate = this.formatDate(start);
                this.endDate = this.formatDate(end);
                this.amount = monthlyRate * months;

                const startStr = ('0' + start.getDate()).slice(-2) + '/' + ('0' + (start.getMonth() + 1)).slice(-2) + '/' + start.getFullYear();
                const endStr = ('0' + end.getDate()).slice(-2) + '/' + ('0' + (end.getMonth() + 1)).slice(-2) + '/' + end.getFullYear();

                if (months === 1) {
                    this.note = `Học phí 1 tháng (${startStr} - ${endStr})`;
                } else {
                    this.note = `Học phí ${months} tháng (${startStr} - ${endStr})`;
                }
            },

            formatDate(d) {
                const year = d.getFullYear();
                const month = ('0' + (d.getMonth() + 1)).slice(-2);
                const day = ('0' + d.getDate()).slice(-2);
                return `${year}-${month}-${day}`;
            }
        };
    }
</script>

<div x-data="tuitionRecordForm()" class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>💳 Ghi Nhận Thu Học Phí</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Lập biên lai thu tiền học phí, tự động tính mốc gia hạn và gửi thông báo xác nhận tới học viên.
            </p>
        </div>

        <a href="{{ route('admin.tuition.index') }}" 
           class="px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">
            ← Quay lại danh sách
        </a>
    </div>

    @if($errors->any())
        <div class="p-4 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm">
            <p class="font-semibold mb-1">Vui lòng kiểm tra lại các trường thông tin:</p>
            <ul class="list-disc list-inside space-y-0.5 text-xs">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.tuition.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- 1. CHỌN HỌC VIÊN & HIỂN THỊ TÓM TẮT THÔNG MINH -->
        <div class="p-5 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-lg space-y-4">
            <div class="flex items-center gap-2 text-emerald-400 text-sm font-bold uppercase tracking-wider">
                <span class="w-6 h-6 rounded-full bg-emerald-500/10 flex items-center justify-center text-xs">1</span>
                <span>Thông tin học viên đóng tiền</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Select student -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Chọn học viên <span class="text-rose-400">*</span>
                    </label>
                    <select name="user_id" x-model="selectedStudentId" @change="onStudentChanged(true)" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                        <option value="">-- Vui lòng chọn học viên --</option>
                        @foreach($students as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} ({{ $s->email }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Select course (Optional) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Áp dụng cho khóa học (Tùy chọn)
                    </label>
                    <select name="course_id" 
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                        <option value="">-- Học phí duy trì chung toàn viện --</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Smart Student Summary Card (Reactive Alpine) -->
            <div x-show="currentStudent" class="p-4 rounded-xl bg-slate-900/90 border border-slate-700/70 space-y-2 mt-3" style="display: none;">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-2.5">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold text-xs" x-text="currentStudent ? currentStudent.name.charAt(0) : ''">
                        </div>
                        <div>
                            <span class="text-sm font-bold text-white block" x-text="currentStudent ? currentStudent.name : ''"></span>
                            <span class="text-xs text-slate-400" x-text="currentStudent ? currentStudent.email : ''"></span>
                        </div>
                    </div>
                    <div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold" :class="currentStudent ? currentStudent.status_badge : ''" x-text="currentStudent ? currentStudent.status_label : ''"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs pt-1">
                    <div>
                        <span class="text-slate-400 block">Hạn nộp hiện tại:</span>
                        <span class="font-bold font-mono text-sm" :class="currentStudent && currentStudent.is_overdue ? 'text-rose-400' : 'text-slate-200'" x-text="currentStudent ? currentStudent.paid_until_formatted : '--'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Mức phí tháng:</span>
                        <span class="font-bold font-mono text-sm text-emerald-400" x-text="currentStudent ? new Intl.NumberFormat('vi-VN').format(currentStudent.monthly_fee) + 'đ' : '--'"></span>
                    </div>
                    <div>
                        <span class="text-slate-400 block">Hình thức hiện tại:</span>
                        <span class="font-medium text-slate-300" x-text="currentStudent ? currentStudent.billing_type : '--'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. CHỌN KỲ HẠN THÔNG MINH (PRESETS 1-CLICK) -->
        <div class="p-5 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-lg space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 text-emerald-400 text-sm font-bold uppercase tracking-wider">
                    <span class="w-6 h-6 rounded-full bg-emerald-500/10 flex items-center justify-center text-xs">2</span>
                    <span>Chọn gói kỳ hạn đóng tiền</span>
                </div>
                <span class="text-xs text-slate-400">Bấm 1-Click để hệ thống tự cộng dồn ngày kết thúc</span>
            </div>

            <!-- Preset Buttons Bar -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                <button type="button" @click="applyPreset('1m', 1)"
                        class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center cursor-pointer"
                        :class="activePreset === '1m' ? 'bg-emerald-950/40 border-emerald-500 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-slate-600'">
                    <span class="text-sm font-bold block">+ 1 Tháng</span>
                    <span class="text-[11px] text-slate-400 mt-0.5">Đóng tháng kế tiếp</span>
                </button>

                <button type="button" @click="applyPreset('3m', 3)"
                        class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center cursor-pointer"
                        :class="activePreset === '3m' ? 'bg-emerald-950/40 border-emerald-500 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-slate-600'">
                    <span class="text-sm font-bold block">+ 3 Tháng</span>
                    <span class="text-[11px] text-slate-400 mt-0.5">Theo quý (Combo)</span>
                </button>

                <button type="button" @click="applyPreset('6m', 6)"
                        class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center cursor-pointer"
                        :class="activePreset === '6m' ? 'bg-emerald-950/40 border-emerald-500 text-emerald-300 ring-1 ring-emerald-500/30' : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-slate-600'">
                    <span class="text-sm font-bold block">+ 6 Tháng</span>
                    <span class="text-[11px] text-slate-400 mt-0.5">Nửa năm</span>
                </button>

                <button type="button" @click="applyPreset('full_course', 1)"
                        class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center cursor-pointer"
                        :class="activePreset === 'full_course' ? 'bg-indigo-950/40 border-indigo-500 text-indigo-300 ring-1 ring-indigo-500/30' : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-slate-600'">
                    <span class="text-sm font-bold block">💎 Trọn gói</span>
                    <span class="text-[11px] text-slate-400 mt-0.5">Học trọn đời</span>
                </button>

                <button type="button" @click="applyPreset('custom', 1)"
                        class="p-3 rounded-xl border text-center transition flex flex-col items-center justify-center cursor-pointer"
                        :class="activePreset === 'custom' ? 'bg-sky-950/40 border-sky-500 text-sky-300 ring-1 ring-sky-500/30' : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-slate-600'">
                    <span class="text-sm font-bold block">✏️ Tùy chỉnh</span>
                    <span class="text-[11px] text-slate-400 mt-0.5">Tự chọn ngày</span>
                </button>
            </div>

            <!-- Inputs Ngày bắt đầu, Ngày kết thúc, Số tháng -->
            <input type="hidden" name="billing_type" x-model="billingType">
            <input type="hidden" name="months_count" x-model="monthsCount">

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Ngày bắt đầu kỳ nộp <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="start_date" x-model="startDate" required
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <div x-show="billingType !== 'full_course'">
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Ngày kết thúc kỳ nộp (Hạn mới) <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="end_date" x-model="endDate" :required="billingType !== 'full_course'"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <div x-show="billingType === 'full_course'" class="p-3 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-300 text-xs flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <span>Học viên sẽ được đánh dấu <strong>Trọn gói cả khóa</strong>, không giới hạn thời gian học và không bao giờ bị báo nợ.</span>
                </div>
            </div>
        </div>

        <!-- 3. SỐ TIỀN & PHƯƠNG THỨC THANH TOÁN -->
        <div class="p-5 rounded-2xl bg-slate-800/90 border border-slate-700/80 shadow-lg space-y-4">
            <div class="flex items-center gap-2 text-emerald-400 text-sm font-bold uppercase tracking-wider">
                <span class="w-6 h-6 rounded-full bg-emerald-500/10 flex items-center justify-center text-xs">3</span>
                <span>Số tiền & Chi tiết thanh toán</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Số tiền -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Số tiền thực thu (VNĐ) <span class="text-rose-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" name="amount" x-model="amount" required min="0" step="1000"
                               class="w-full pl-3.5 pr-14 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white font-mono text-base font-bold focus:outline-none focus:border-emerald-500">
                        <span class="absolute right-3.5 top-2.5 text-xs font-semibold text-slate-400">VNĐ</span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">Admin có thể tùy chỉnh số tiền nếu có giảm giá / học bổng.</p>
                </div>

                <!-- Phương thức -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Phương thức thanh toán <span class="text-rose-400">*</span>
                    </label>
                    <select name="payment_method" required
                            class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                        <option value="bank_transfer" selected>🏦 Chuyển khoản ngân hàng</option>
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="crypto">🪙 USDT / Crypto</option>
                        <option value="other">Khác</option>
                    </select>
                </div>

                <!-- Mã giao dịch -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Mã giao dịch ngân hàng / Ref Code
                    </label>
                    <input type="text" name="transaction_code" value="{{ old('transaction_code') }}"
                           placeholder="Ví dụ: FT261019123456"
                           class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
                </div>

                <!-- Ảnh bill -->
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                        Ảnh chụp bill chuyển khoản (Tối đa 10MB)
                    </label>
                    <input type="file" name="payment_proof" accept="image/*"
                           class="w-full text-xs text-slate-400 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-700 file:text-emerald-400 hover:file:bg-slate-600 file:cursor-pointer cursor-pointer">
                </div>
            </div>

            <!-- Ghi chú -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-1.5">
                    Ghi chú đợt thu
                </label>
                <input type="text" name="note" x-model="note"
                       placeholder="Ví dụ: Đóng học phí Tháng 11/2026 qua Techcombank..."
                       class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-sm focus:outline-none focus:border-emerald-500">
            </div>
        </div>

        <!-- Buttons -->
        <div class="flex items-center justify-end gap-3 pt-2">
            <a href="{{ route('admin.tuition.index') }}" 
               class="px-5 py-2.5 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition">
                Hủy bỏ
            </a>

            <button type="submit" 
                    class="px-6 py-2.5 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Xác nhận thu tiền & Cập nhật kỳ hạn</span>
            </button>
        </div>
    </form>
</div>
@endsection

