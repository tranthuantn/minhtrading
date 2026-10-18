@extends('layouts.admin')

@section('content')
@php
    $pair = strtoupper($trade->pair ?? $trade->currency_pair ?? 'EURUSD');
    $direction = strtoupper($trade->direction ?? 'BUY');
    $timeframe = $trade->timeframe ?? 'H1';
    
    // RR calculation
    $rr = $trade->rr_ratio;
    if (!$rr && !empty($trade->entry_price) && !empty($trade->stop_loss) && !empty($trade->take_profit)) {
        $risk = abs($trade->entry_price - $trade->stop_loss);
        $reward = abs($trade->take_profit - $trade->entry_price);
        if ($risk > 0) {
            $rr = round($reward / $risk, 1);
        }
    }
    $rrText = $rr ? '1:' . $rr : '1:3.0';

    // Chart image resolution
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

    // Criteria format
    $criteriaText = '';
    if (!empty($trade->setup_criteria)) {
        if (is_array($trade->setup_criteria)) {
            $criteriaText = implode("\n", array_map(fn($k, $v) => is_numeric($k) ? "• $v" : "• $k: $v", array_keys($trade->setup_criteria), $trade->setup_criteria));
        } else {
            $criteriaText = $trade->setup_criteria;
        }
    } else {
        $criteriaText = "• Cấu trúc xu hướng lớn: Rõ ràng (Trend confirmation)\n• Vùng phản ứng giá: Order Block / FVG / Hỗ trợ - Kháng cự\n• Tín hiệu kích hoạt: Nến đảo chiều / Liquidity Sweep / CHoCH\n• Quản lý vốn: Rủi ro tối đa 1% tài khoản\n• Tỷ lệ R:R tối thiểu: 1:2.5+";
    }
@endphp

<div x-data="{ showZoom: false }" class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" class="hover:text-white">Mô hình mẫu</a>
                <span>/</span>
                <span class="text-emerald-400">Chi tiết mô hình #{{ $trade->id }}</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex flex-wrap items-center gap-3">
                <span>{{ $trade->title ?? 'Mô hình chuẩn' }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold {{ ($trade->is_published ?? true) ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-slate-700 text-slate-400' }}">
                    {{ ($trade->is_published ?? true) ? 'Đã xuất bản' : 'Bản nháp' }}
                </span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Nguồn gốc: 
                @if(!empty($trade->source_trade_entry_id) || !empty($trade->trade_entry_id) || !empty($trade->sourceTrade))
                    <span class="text-sky-400 font-semibold">Lệnh thực tế từ Học viên</span>
                @else
                    <span class="text-amber-400 font-semibold">Giảng viên / Admin biên soạn</span>
                @endif
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.model-trades.edit') ? route('admin.model-trades.edit', $trade->id) : url('/admin/model-trades/' . $trade->id . '/edit') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-amber-600 hover:bg-amber-500 text-white shadow-lg shadow-amber-600/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chỉnh sửa
            </a>
            <a href="{{ Route::has('admin.model-trades.index') ? route('admin.model-trades.index') : url('/admin/model-trades') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition">
                &larr; Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Trade Parameters Grid (Full detail view) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Cặp tiền</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $pair }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Vị thế & Khung TG</span>
            <span class="text-base font-bold font-mono mt-1 block {{ $direction === 'BUY' ? 'text-emerald-400' : 'text-rose-400' }}">
                {{ $direction }} ({{ $timeframe }})
            </span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Điểm vào (Entry)</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $trade->entry_price ?? 'N/A' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Cắt lỗ (Stop Loss)</span>
            <span class="text-base font-bold font-mono text-rose-400 mt-1 block">{{ $trade->stop_loss ?? 'N/A' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Chốt lời (Take Profit)</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">{{ $trade->take_profit ?? 'N/A' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Tỷ lệ Risk:Reward</span>
            <span class="text-base font-bold font-mono text-sky-400 mt-1 block">{{ $rrText }}</span>
        </div>
    </div>

    <!-- Chart Screenshot Display with Zoom Trigger -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-sky-400">📊</span>
                Biểu đồ phân tích kỹ thuật
            </h3>
            @if(!empty($imgUrl))
                <button @click="showZoom = true" type="button" class="text-xs font-semibold text-emerald-400 hover:text-emerald-300 flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                    Phóng to biểu đồ
                </button>
            @endif
        </div>

        <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-950 flex items-center justify-center p-2 min-h-64">
            @if(!empty($imgUrl))
                <img src="{{ $imgUrl }}" alt="{{ $trade->title }}" 
                     @click="showZoom = true"
                     class="max-h-[520px] w-auto max-w-full object-contain cursor-zoom-in rounded hover:opacity-95 transition">
            @else
                <div class="py-16 text-center text-slate-500">
                    <svg class="w-16 h-16 mx-auto mb-2 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    <p class="text-sm font-semibold text-slate-300">Chưa có ảnh biểu đồ đính kèm cho mô hình này</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Analysis & Notes Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detailed Analysis (2 cols) -->
        <div class="lg:col-span-2 rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-slate-700/60">
                <span class="text-emerald-400">🧠</span>
                Lý do vào lệnh & Phân tích chuyên sâu
            </h3>
            
            @if(!empty($trade->description))
                <div class="p-3.5 rounded-lg bg-slate-900/80 border border-slate-800 text-xs text-slate-300 leading-relaxed">
                    <span class="text-emerald-400 font-bold block mb-1">Tóm tắt mô hình:</span>
                    {{ $trade->description }}
                </div>
            @endif

            <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-200 leading-relaxed whitespace-pre-line font-sans">
                {{ $trade->analysis ?? $trade->explanation ?? "1. Khung thời gian lớn (HTF): Giá đang trong xu hướng tăng rõ rệt với cấu trúc đỉnh đáy cao dần. Giá hồi về kiểm tra vùng Order Block / Demand Zone quan trọng.\n\n2. Khung xác nhận (LTF): Xuất hiện phản ứng quét thanh khoản (Liquidity Sweep) và tạo cấu trúc đảo chiều CHoCH mạnh mẽ.\n\n3. Điểm vào lệnh: Đặt lệnh Buy Limit tại Fair Value Gap (FVG) hợp lưu với mức thoái lui Fibonacci OTE (0.618 - 0.705).\n\n4. Quản lý lệnh: Cắt lỗ đặt dưới râu nến quét thanh khoản. Chốt lời theo từng mức thanh khoản đỉnh cũ." }}
            </div>

            @if(!empty($trade->notes))
                <div class="p-3.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200 leading-relaxed">
                    <span class="font-bold block mb-1">Lưu ý tâm lý & quản trị:</span>
                    {{ $trade->notes }}
                </div>
            @endif
        </div>

        <!-- Checklist Criteria (1 col) -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-slate-700/60">
                <span class="text-amber-400">✅</span>
                Checklist tiêu chí cần nhớ
            </h3>
            <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 leading-relaxed whitespace-pre-line font-mono">
                {{ $criteriaText }}
            </div>
        </div>
    </div>

    <!-- ZOOM POPUP MODAL -->
    @if(!empty($imgUrl))
        <div x-show="showZoom" 
             x-cloak 
             @keydown.escape.window="showZoom = false"
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/90 backdrop-blur-md">
            <div @click.away="showZoom = false" class="max-w-5xl w-full flex flex-col items-center">
                <div class="w-full flex justify-end mb-2">
                    <button @click="showZoom = false" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold">
                        Đóng [ESC]
                    </button>
                </div>
                <img src="{{ $imgUrl }}" alt="{{ $trade->title }}" class="max-h-[85vh] w-auto max-w-full rounded-xl shadow-2xl border border-slate-700 object-contain">
            </div>
        </div>
    @endif
</div>
@endsection
