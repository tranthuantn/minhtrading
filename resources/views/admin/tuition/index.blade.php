@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>💰 Quản Lý Học Phí & Cảnh Báo Nợ</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Theo dõi kỳ hạn đóng tiền của từng học viên (theo tháng, nhiều tháng, trọn gói), tự động phát hiện nợ và cảnh báo quá hạn.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.tuition.history') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                <span>Lịch sử thu tiền</span>
            </a>

            <a href="{{ route('admin.tuition.create') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Ghi nhận thu tiền</span>
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- Tổng học viên -->
        <a href="{{ route('admin.tuition.index') }}" 
           class="p-3.5 rounded-xl bg-slate-800/90 border {{ !request('status') ? 'border-sky-500/50 bg-slate-800 ring-1 ring-sky-500/30' : 'border-slate-700/80' }} hover:border-sky-500/50 transition">
            <span class="text-[11px] text-slate-400 block mb-1">Tổng học viên</span>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold font-mono text-white">{{ $stats['total'] }}</span>
                <span class="p-1.5 rounded-lg bg-slate-900 text-sky-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </span>
            </div>
        </a>

        <!-- Đã đóng đủ / Còn hạn -->
        <a href="{{ route('admin.tuition.index', array_filter(['status' => 'active', 'course_id' => request('course_id')])) }}" 
           class="p-3.5 rounded-xl bg-slate-800/90 border {{ request('status') === 'active' ? 'border-emerald-500/50 bg-slate-800 ring-1 ring-emerald-500/30' : 'border-slate-700/80' }} hover:border-emerald-500/50 transition">
            <span class="text-[11px] text-slate-400 block mb-1">Đã đóng đủ (>5 ngày)</span>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold font-mono text-emerald-400">{{ $stats['active'] }}</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </span>
            </div>
        </a>

        <!-- Sắp hết hạn -->
        <a href="{{ route('admin.tuition.index', array_filter(['status' => 'due_soon', 'course_id' => request('course_id')])) }}" 
           class="p-3.5 rounded-xl bg-slate-800/90 border {{ request('status') === 'due_soon' ? 'border-amber-500/50 bg-slate-800 ring-1 ring-amber-500/30' : 'border-slate-700/80' }} hover:border-amber-500/50 transition">
            <span class="text-[11px] text-slate-400 block mb-1">Sắp hết hạn (≤ 5 ngày)</span>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold font-mono text-amber-400">{{ $stats['due_soon'] }}</span>
                <span class="p-1.5 rounded-lg bg-amber-500/10 text-amber-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
            </div>
        </a>

        <!-- Nợ tháng này -->
        <a href="{{ route('admin.tuition.index', array_filter(['status' => 'overdue', 'course_id' => request('course_id')])) }}" 
           class="p-3.5 rounded-xl bg-slate-800/90 border {{ request('status') === 'overdue' ? 'border-rose-500/50 bg-slate-800 ring-1 ring-rose-500/30' : 'border-slate-700/80' }} hover:border-rose-500/50 transition">
            <span class="text-[11px] text-slate-400 block mb-1">Chưa nộp tháng này</span>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold font-mono text-rose-400">{{ $stats['overdue'] }}</span>
                <span class="p-1.5 rounded-lg bg-rose-500/10 text-rose-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
            </div>
        </a>

        <!-- Nợ nhiều tháng -->
        <a href="{{ route('admin.tuition.index', array_filter(['status' => 'severely_overdue', 'course_id' => request('course_id')])) }}" 
           class="p-3.5 rounded-xl bg-slate-800/90 border {{ request('status') === 'severely_overdue' ? 'border-red-500/50 bg-slate-800 ring-1 ring-red-500/30' : 'border-slate-700/80' }} hover:border-red-500/50 transition">
            <span class="text-[11px] text-slate-400 block mb-1">Nợ nhiều tháng (>30 ngày)</span>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold font-mono text-rose-300">{{ $stats['severely_overdue'] }}</span>
                <span class="p-1.5 rounded-lg bg-red-950/40 text-rose-300 border border-rose-500/30">
                    <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                </span>
            </div>
        </a>

        <!-- Trọn gói cả khóa -->
        <a href="{{ route('admin.tuition.index', array_filter(['status' => 'full_course', 'course_id' => request('course_id')])) }}" 
           class="p-3.5 rounded-xl bg-slate-800/90 border {{ request('status') === 'full_course' ? 'border-indigo-500/50 bg-slate-800 ring-1 ring-indigo-500/30' : 'border-slate-700/80' }} hover:border-indigo-500/50 transition">
            <span class="text-[11px] text-slate-400 block mb-1">Trọn gói cả khóa</span>
            <div class="flex items-center justify-between">
                <span class="text-xl font-bold font-mono text-indigo-400">{{ $stats['full_course'] }}</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                </span>
            </div>
        </a>
    </div>

    <!-- Doanh thu tháng này banner -->
    <div class="p-4 rounded-xl bg-gradient-to-r from-emerald-950/30 via-slate-800 to-slate-800 border border-emerald-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div class="flex items-center gap-3">
            <span class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </span>
            <div>
                <span class="text-xs text-slate-400">Học phí đã thu trong Tháng {{ now()->format('m/Y') }}:</span>
                <div class="text-xl font-bold font-mono text-emerald-400">
                    {{ number_format($stats['revenue_this_month'], 0, ',', '.') }} VNĐ
                </div>
            </div>
        </div>
        <div class="text-xs text-slate-400">
            Hệ thống tự động đồng bộ kỳ hạn và gửi thông báo biên lai tới học viên ngay sau khi ghi nhận.
        </div>
    </div>

    <!-- Filters Bar -->
    <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg space-y-3.5">
        <form method="GET" action="{{ route('admin.tuition.index') }}" class="flex flex-col md:flex-row md:items-center justify-between gap-3">
            <!-- Trạng thái học phí Pills -->
            <div class="flex flex-wrap items-center gap-1.5">
                <a href="{{ route('admin.tuition.index', array_filter(['course_id' => request('course_id'), 'search' => request('search')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ !request('status') ? 'bg-slate-700 text-white shadow-sm' : 'bg-slate-900 text-slate-400 hover:text-white border border-slate-800' }}">
                    Tất cả ({{ $stats['total'] }})
                </a>

                <a href="{{ route('admin.tuition.index', array_filter(['status' => 'overdue', 'course_id' => request('course_id'), 'search' => request('search')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1 {{ request('status') === 'overdue' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/50' : 'bg-slate-900 text-slate-400 hover:text-rose-400 border border-slate-800' }}">
                    <span>🔴 Chưa nộp tháng này</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('status') === 'overdue' ? 'bg-rose-900/60 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $stats['overdue'] }}</span>
                </a>

                <a href="{{ route('admin.tuition.index', array_filter(['status' => 'severely_overdue', 'course_id' => request('course_id'), 'search' => request('search')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1 {{ request('status') === 'severely_overdue' ? 'bg-red-950/40 text-rose-300 border border-red-500/50' : 'bg-slate-900 text-slate-400 hover:text-rose-400 border border-slate-800' }}">
                    <span>⛔ Nợ nhiều tháng</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('status') === 'severely_overdue' ? 'bg-red-900/60 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $stats['severely_overdue'] }}</span>
                </a>

                <a href="{{ route('admin.tuition.index', array_filter(['status' => 'due_soon', 'course_id' => request('course_id'), 'search' => request('search')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1 {{ request('status') === 'due_soon' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/50' : 'bg-slate-900 text-slate-400 hover:text-amber-400 border border-slate-800' }}">
                    <span>🟡 Sắp hết hạn</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('status') === 'due_soon' ? 'bg-amber-900/60 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $stats['due_soon'] }}</span>
                </a>

                <a href="{{ route('admin.tuition.index', array_filter(['status' => 'active', 'course_id' => request('course_id'), 'search' => request('search')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1 {{ request('status') === 'active' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/50' : 'bg-slate-900 text-slate-400 hover:text-emerald-400 border border-slate-800' }}">
                    <span>🟢 Đã đóng đủ</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('status') === 'active' ? 'bg-emerald-900/60 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $stats['active'] }}</span>
                </a>

                <a href="{{ route('admin.tuition.index', array_filter(['status' => 'full_course', 'course_id' => request('course_id'), 'search' => request('search')])) }}" 
                   class="px-3 py-1.5 rounded-lg text-xs font-semibold transition flex items-center gap-1 {{ request('status') === 'full_course' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/50' : 'bg-slate-900 text-slate-400 hover:text-indigo-400 border border-slate-800' }}">
                    <span>💎 Trọn gói</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] {{ request('status') === 'full_course' ? 'bg-indigo-900/60 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $stats['full_course'] }}</span>
                </a>
            </div>

            <!-- Khóa học & Search -->
            <div class="flex items-center gap-2">
                @if(request('status'))
                    <input type="hidden" name="status" value="{{ request('status') }}">
                @endif

                <select name="course_id" onchange="this.form.submit()" 
                        class="px-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-700 text-white focus:outline-none focus:border-emerald-500">
                    <option value="">-- Tất cả khóa học --</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" {{ request('course_id') == $c->id ? 'selected' : '' }}>{{ Str::limit($c->title, 25) }}</option>
                    @endforeach
                </select>

                <div class="relative w-48 sm:w-56">
                    <input type="text" name="search" value="{{ request('search') }}" 
                           placeholder="Tên, email, SĐT..."
                           class="w-full pl-8 pr-3 py-1.5 rounded-lg text-xs bg-slate-900 border border-slate-700 text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    <svg class="w-3.5 h-3.5 text-slate-500 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-700 hover:bg-slate-600 text-white transition">
                    Lọc
                </button>

                @if(request('status') || request('course_id') || request('search'))
                    <a href="{{ route('admin.tuition.index') }}" class="text-xs text-sky-400 hover:text-sky-300 whitespace-nowrap">
                        ✕ Xóa lọc
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Students Tuition Table -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-slate-300">
                <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase tracking-wider border-b border-slate-700/50 font-semibold">
                    <tr>
                        <th class="py-3.5 px-4">Học viên</th>
                        <th class="py-3.5 px-4">Khóa học & Hình thức</th>
                        <th class="py-3.5 px-4 text-center">Hạn đóng tiền</th>
                        <th class="py-3.5 px-4 text-center">Tình trạng học phí</th>
                        <th class="py-3.5 px-4">Đợt đóng gần nhất</th>
                        <th class="py-3.5 px-4 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">
                    @forelse($students as $st)
                        @php
                            $tuition = $st->tuition_status;
                            $lastPayment = $st->tuitionPayments->first();
                        @endphp
                        <tr class="hover:bg-slate-750/50 transition">
                            <!-- Student Info -->
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-slate-700 flex items-center justify-center font-bold text-sm text-white shrink-0">
                                        {{ strtoupper(substr($st->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('admin.students.show', $st->id) }}" class="font-bold text-white hover:text-emerald-400 transition block">
                                            {{ $st->name }}
                                        </a>
                                        <div class="text-xs text-slate-400 flex items-center gap-2">
                                            <span>{{ $st->email }}</span>
                                            @if($st->phone)
                                                <span>• {{ $st->phone }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Courses & Billing type -->
                            <td class="py-3 px-4">
                                <div class="space-y-1">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($st->enrolledCourses as $c)
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-900 text-sky-400 border border-slate-700">
                                                {{ Str::limit($c->title, 20) }}
                                            </span>
                                        @empty
                                            <span class="text-xs text-slate-500 italic">Chưa gắn khóa</span>
                                        @endforelse
                                    </div>
                                    <div class="text-xs text-slate-400">
                                        @if($st->billing_type === 'full_course')
                                            <span class="text-indigo-400 font-medium">💎 Đóng trọn gói cả khóa</span>
                                        @elseif($st->billing_type === 'free')
                                            <span class="text-purple-400 font-medium">🎁 Miễn học phí (Học bổng)</span>
                                        @else
                                            <span>Mức phí: <strong class="text-white">{{ number_format($st->monthly_tuition_fee, 0, ',', '.') }}đ/tháng</strong></span>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Paid Until -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                @if($st->billing_type === 'full_course')
                                    <span class="text-xs text-emerald-400 font-semibold font-mono">Trọn đời</span>
                                @elseif($st->billing_type === 'free')
                                    <span class="text-xs text-purple-400 font-semibold font-mono">Không thời hạn</span>
                                @elseif($st->paid_until)
                                    <div class="font-mono text-sm font-bold {{ $tuition['is_overdue'] ? 'text-rose-400' : 'text-slate-200' }}">
                                        {{ $st->paid_until->format('d/m/Y') }}
                                    </div>
                                    <span class="text-[11px] text-slate-500 block">
                                        {{ $st->paid_until->diffForHumans() }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-500 italic">Chưa có ngày</span>
                                @endif
                            </td>

                            <!-- Status Badge with Explanation -->
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold inline-block {{ $tuition['badge_class'] }}">
                                    {{ $tuition['label'] }}
                                </span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                    {{ $tuition['description'] }}
                                </span>
                            </td>

                            <!-- Last Payment -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($lastPayment)
                                    <div>
                                        <span class="text-xs font-mono font-bold text-emerald-400">
                                            +{{ number_format($lastPayment->amount, 0, ',', '.') }}đ
                                        </span>
                                        <span class="text-[11px] text-slate-500 block">
                                            {{ $lastPayment->created_at->format('d/m/Y') }} ({{ $lastPayment->billing_type_label }})
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-500 italic">Chưa có giao dịch</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <div class="inline-flex items-center gap-1.5">
                                    <!-- Thu tiền ngay -->
                                    <a href="{{ route('admin.tuition.create', ['student_id' => $st->id]) }}" 
                                       class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow transition flex items-center gap-1"
                                       title="Ghi nhận thu tiền học phí">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                        <span>Thu tiền</span>
                                    </a>

                                    <!-- Nhắc phí nếu nợ hoặc sắp hết hạn -->
                                    @if($tuition['is_overdue'] || $tuition['code'] === 'due_soon')
                                        <form method="POST" action="{{ route('admin.tuition.remind', $st->id) }}" onsubmit="return confirm('Bạn có chắc muốn gửi thông báo nhắc học phí đến học viên {{ $st->name }}?')">
                                            @csrf
                                            <button type="submit" 
                                                    class="px-2.5 py-1.5 rounded-lg text-xs font-semibold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition flex items-center gap-1"
                                                    title="Gửi thông báo nhắc đóng tiền 1-Click">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                                                <span>Nhắc phí</span>
                                            </button>
                                        </form>
                                    @endif

                                    <!-- Xem lịch sử -->
                                    <a href="{{ route('admin.tuition.student', $st->id) }}" 
                                       class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-700 transition"
                                       title="Xem lịch sử đóng tiền của học viên">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-500">
                                Không tìm thấy học viên nào phù hợp với bộ lọc.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($students->hasPages())
            <div class="p-4 border-t border-slate-700/50">
                {{ $students->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

