@extends('layouts.student')

@section('content')
<div x-data="{
    showModal: false,
    activeImg: '',
    activeTitle: '',
    activePair: '',
    activeDirection: '',
    activeTimeframe: '',
    activeReason: '',
    detailUrl: '',
    openPreview(img, title, pair, direction, timeframe, reason, url) {
        this.activeImg = img;
        this.activeTitle = title;
        this.activePair = pair;
        this.activeDirection = direction;
        this.activeTimeframe = timeframe;
        this.activeReason = reason;
        this.detailUrl = url;
        this.showModal = true;
    }
}" @keydown.escape.window="showModal = false" class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-2">
                <span>🏆 Thư Viện Mô Hình Lệnh Mẫu</span>
                <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30">Case Studies Thực Chiến</span>
            </h1>
            <p class="text-sm text-slate-400 mt-1">Các case study giao dịch chuẩn mực từ Giảng viên và học viên xuất sắc để bạn rèn luyện tư duy</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="rounded-xl bg-slate-800/80 border border-slate-700/80 p-4 shadow-sm">
        <form method="GET" action="{{ Route::has('student.model-trades.index') ? route('student.model-trades.index') : url('/student/model-trades') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <input type="text" name="pair" value="{{ request('pair') }}" placeholder="Lọc theo cặp tiền (EURUSD, XAUUSD...)" 
                       class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs font-mono placeholder:text-slate-500 focus:outline-none focus:border-amber-500">
            </div>

            <div>
                <select name="direction" class="w-full px-3 py-2 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-amber-500">
                    <option value="">-- Tất cả vị thế (BUY/SELL) --</option>
                    <option value="buy" {{ strtolower(request('direction')) == 'buy' ? 'selected' : '' }}>BUY (Mua)</option>
                    <option value="sell" {{ strtolower(request('direction')) == 'sell' ? 'selected' : '' }}>SELL (Bán)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 text-white text-xs font-semibold transition">
                    Lọc mô hình
                </button>
                @if(request()->hasAny(['pair', 'direction']))
                    <a href="{{ Route::has('student.model-trades.index') ? route('student.model-trades.index') : url('/student/model-trades') }}" 
                       class="px-3 py-2 rounded-lg bg-slate-900 hover:bg-slate-800 text-slate-400 text-xs transition">
                        Đặt lại
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900/90 border-b border-slate-700 text-[11px] font-semibold uppercase tracking-wider text-slate-400">
                        <th class="py-3.5 px-4 w-24 text-center">Hình ảnh</th>
                        <th class="py-3.5 px-4">Cặp tiền</th>
                        <th class="py-3.5 px-4">Vị thế</th>
                        <th class="py-3.5 px-4">Khung TG</th>
                        <th class="py-3.5 px-4 min-w-[280px]">Mô hình & Lý do vào lệnh</th>
                        <th class="py-3.5 px-4">Nguồn gốc</th>
                        <th class="py-3.5 px-4 text-right">Chi tiết</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/60 text-xs">
                    @forelse($modelTrades ?? [] as $trade)
                        @php
                            $pair = strtoupper($trade->pair ?? $trade->currency_pair ?? 'EURUSD');
                            $direction = strtoupper($trade->direction ?? 'BUY');
                            $timeframe = $trade->timeframe ?? 'H1';
                            $title = $trade->title ?? 'Mô hình chuẩn';
                            $reason = $trade->setup_name 
                                ?? (!empty($trade->description) ? Str::limit($trade->description, 160) : (!empty($trade->analysis) ? Str::limit($trade->analysis, 160) : 'Setup chuẩn cấu trúc thị trường & thanh khoản'));
                            
                            // Image resolution
                            $imgUrl = '';
                            if (!empty($trade->chart_image)) {
                                $imgUrl = str_starts_with($trade->chart_image, 'http') ? $trade->chart_image : asset('storage/' . $trade->chart_image);
                            } elseif (!empty($trade->chart_screenshots) && is_array($trade->chart_screenshots) && count($trade->chart_screenshots) > 0) {
                                $first = $trade->chart_screenshots[0];
                                $imgUrl = str_starts_with($first, 'http') ? $first : asset('storage/' . $first);
                            } elseif (!empty($trade->sourceTrade?->chart_before_url)) {
                                $first = $trade->sourceTrade->chart_before_url;
                                $imgUrl = str_starts_with($first, 'http') ? $first : asset('storage/' . $first);
                            }
                            
                            $showUrl = Route::has('student.model-trades.show') ? route('student.model-trades.show', $trade->id) : url('/student/model-trades/' . $trade->id);
                        @endphp
                        <tr class="hover:bg-slate-700/30 transition-colors">
                            <!-- Thumbnail with popup trigger -->
                            <td class="py-3 px-4 text-center">
                                @if(!empty($imgUrl))
                                    <div class="relative group cursor-pointer inline-block"
                                         @click="openPreview('{{ $imgUrl }}', '{{ addslashes($title) }}', '{{ $pair }}', '{{ $direction }}', '{{ $timeframe }}', '{{ addslashes($reason) }}', '{{ $showUrl }}')">
                                        <img src="{{ $imgUrl }}" alt="{{ $title }}" 
                                             class="w-16 h-11 object-cover rounded-lg border border-slate-700 group-hover:border-amber-400 shadow-sm transition-all group-hover:scale-105">
                                        <div class="absolute inset-0 bg-slate-950/50 opacity-0 group-hover:opacity-100 rounded-lg flex items-center justify-center transition-opacity">
                                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                                        </div>
                                    </div>
                                @else
                                    <button type="button" 
                                            @click="openPreview('', '{{ addslashes($title) }}', '{{ $pair }}', '{{ $direction }}', '{{ $timeframe }}', '{{ addslashes($reason) }}', '{{ $showUrl }}')"
                                            class="w-16 h-11 rounded-lg border border-slate-700/80 bg-slate-900/80 flex flex-col items-center justify-center text-slate-500 hover:text-amber-400 hover:border-amber-400/50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="text-[9px] font-sans">Chart</span>
                                    </button>
                                @endif
                            </td>

                            <!-- Pair -->
                            <td class="py-3 px-4 font-mono font-bold text-white whitespace-nowrap">
                                <span class="px-2 py-1 rounded bg-slate-900 border border-slate-700/80 text-xs">
                                    {{ $pair }}
                                </span>
                            </td>

                            <!-- Position: BUY or SELL -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-md text-xs font-bold font-mono {{ $direction === 'BUY' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                                    {{ $direction }}
                                </span>
                            </td>

                            <!-- Timeframe -->
                            <td class="py-3 px-4 whitespace-nowrap">
                                <span class="px-2 py-1 rounded text-xs font-mono font-semibold bg-slate-900/90 text-sky-400 border border-sky-500/30">
                                    {{ $timeframe }}
                                </span>
                            </td>

                            <!-- Title & Entry Reason -->
                            <td class="py-3 px-4">
                                <a href="{{ $showUrl }}" class="font-bold text-slate-100 hover:text-amber-400 transition-colors block text-sm">
                                    {{ $title }}
                                </a>
                                <p class="text-xs text-slate-400 mt-1 line-clamp-2 leading-relaxed">
                                    {{ $reason }}
                                </p>
                            </td>

                            <!-- Source -->
                            <td class="py-3 px-4 whitespace-nowrap text-xs">
                                @if(!empty($trade->source_trade_entry_id) || !empty($trade->trade_entry_id) || !empty($trade->sourceTrade))
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-sky-500/10 text-sky-400 border border-sky-500/30 text-[11px] font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                        Học viên
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-amber-500/10 text-amber-400 border border-amber-500/30 text-[11px] font-medium">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                        Giảng viên
                                    </span>
                                @endif
                            </td>

                            <!-- Action: Xem chi tiết -->
                            <td class="py-3 px-4 text-right whitespace-nowrap">
                                <a href="{{ $showUrl }}" 
                                   class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 font-semibold text-xs transition" 
                                   title="Xem toàn bộ thông số và phân tích chi tiết">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <svg class="w-12 h-12 mx-auto text-slate-600 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <p class="text-sm font-semibold text-slate-300">Chưa có mô hình lệnh mẫu nào được xuất bản</p>
                                <p class="text-xs text-slate-500 mt-1">Giảng viên sẽ sớm cập nhật các case study thực chiến vào thư viện</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($modelTrades) && method_exists($modelTrades, 'hasPages') && $modelTrades->hasPages())
            <div class="p-4 border-t border-slate-700 bg-slate-900/60">
                {{ $modelTrades->links() }}
            </div>
        @endif
    </div>

    <!-- POPUP MODAL: Phóng to ảnh biểu đồ -->
    <div x-show="showModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/85 backdrop-blur-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div @click.away="showModal = false" 
             class="bg-slate-900 border border-slate-700 rounded-2xl max-w-4xl w-full shadow-2xl overflow-hidden flex flex-col max-h-[92vh]"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">

            <!-- Modal Header -->
            <div class="px-5 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950">
                <div class="flex items-center gap-3">
                    <span class="px-2.5 py-1 rounded bg-slate-800 border border-slate-700 font-mono font-bold text-white text-xs" x-text="activePair"></span>
                    <span class="px-2.5 py-1 rounded font-mono font-bold text-xs" 
                          :class="activeDirection === 'BUY' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-rose-500/20 text-rose-400 border border-rose-500/40'"
                          x-text="activeDirection"></span>
                    <span class="px-2 py-0.5 rounded text-xs font-mono bg-slate-800 text-sky-400 border border-sky-500/30" x-text="activeTimeframe"></span>
                    <h3 class="text-sm font-bold text-white truncate max-w-md hidden sm:block" x-text="activeTitle"></h3>
                </div>

                <button @click="showModal = false" class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Image Body -->
            <div class="p-4 overflow-y-auto flex-1 flex flex-col items-center justify-center bg-slate-950/60 min-h-[300px]">
                <template x-if="activeImg">
                    <img :src="activeImg" :alt="activeTitle" class="max-h-[62vh] w-auto max-w-full rounded-lg object-contain shadow-xl border border-slate-800">
                </template>
                <template x-if="!activeImg">
                    <div class="py-16 text-center text-slate-500">
                        <svg class="w-16 h-16 mx-auto mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <p class="text-sm font-semibold text-slate-400">Chưa có ảnh biểu đồ đính kèm</p>
                    </div>
                </template>
            </div>

            <!-- Modal Footer: Quick reason & View details button -->
            <div class="px-5 py-3.5 border-t border-slate-800 bg-slate-900/90 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div class="text-xs text-slate-300">
                    <span class="text-amber-400 font-semibold">Lý do setup:</span>
                    <span x-text="activeReason" class="text-slate-400"></span>
                </div>

                <a :href="detailUrl" class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-semibold text-xs transition shadow-lg shadow-amber-500/20 whitespace-nowrap">
                    Xem toàn bộ thông số & chú thích &rarr;
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
