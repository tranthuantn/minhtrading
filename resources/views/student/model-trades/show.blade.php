@extends('layouts.student')

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

<div x-data="{ showZoom: false }" class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('student.model-trades.index') ? route('student.model-trades.index') : url('/student/model-trades') }}" class="hover:text-white">Mô hình mẫu</a>
                <span>/</span>
                <span class="text-amber-400">Case Study chi tiết</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex flex-wrap items-center gap-3">
                <span>{{ $trade->title ?? 'Mô hình SMC Liquidity Sweep & CHoCH' }}</span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                    Case Study Chuẩn
                </span>
            </h1>
            <p class="text-xs text-slate-400 mt-1">
                Nguồn: 
                @if(!empty($trade->source_trade_entry_id) || !empty($trade->trade_entry_id) || !empty($trade->sourceTrade))
                    <span class="text-sky-400 font-semibold">Lệnh thực tế xuất sắc từ Học viên</span>
                @else
                    <span class="text-amber-400 font-semibold">Giảng viên MinhTrading</span>
                @endif
            </p>
        </div>

        <a href="{{ Route::has('student.model-trades.index') ? route('student.model-trades.index') : url('/student/model-trades') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Quay lại thư viện
        </a>
    </div>

    <!-- Trade Parameters in Clean Study Grid (All parameters visible) -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Cặp tiền</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $pair }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Vị thế</span>
            <span class="text-base font-bold font-mono mt-1 block {{ $direction === 'BUY' ? 'text-emerald-400' : 'text-rose-400' }}">
                {{ $direction }} ({{ $timeframe }})
            </span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Giá Entry</span>
            <span class="text-base font-bold font-mono text-white mt-1 block">{{ $trade->entry_price ?? 'N/A' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Stop Loss</span>
            <span class="text-base font-bold font-mono text-rose-400 mt-1 block">{{ $trade->stop_loss ?? 'N/A' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Take Profit</span>
            <span class="text-base font-bold font-mono text-emerald-400 mt-1 block">{{ $trade->take_profit ?? 'N/A' }}</span>
        </div>

        <div class="p-3.5 rounded-xl bg-slate-800/90 border border-slate-700/80">
            <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-semibold">Tỷ lệ Risk:Reward</span>
            <span class="text-base font-bold font-mono text-sky-400 mt-1 block">{{ $rrText }}</span>
        </div>
    </div>

    <!-- Chart Image Display with Zoom -->
    <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <span class="text-sky-400">📊</span>
                Biểu đồ phân tích kỹ thuật
            </h3>
            @if(!empty($imgUrl))
                <button @click="showZoom = true" type="button" class="text-xs font-semibold text-amber-400 hover:text-amber-300 flex items-center gap-1 transition">
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
                    <p class="text-sm font-semibold text-slate-300">Biểu đồ kỹ thuật phân tích vị thế vào lệnh mẫu</p>
                    <p class="text-xs text-slate-500 mt-1">Nghiên cứu kỹ cách giá phản ứng với các vùng cản</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Detailed Analysis & Criteria -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detailed Analysis (2 cols) -->
        <div class="lg:col-span-2 rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-4">
            <h3 class="text-sm font-bold text-white flex items-center gap-2 pb-3 border-b border-slate-700/60">
                <span class="text-emerald-400">🧠</span>
                Bài học & Phân tích từ Giảng viên
            </h3>
            
            @if(!empty($trade->description))
                <div class="p-3.5 rounded-lg bg-slate-900/80 border border-slate-800 text-xs text-slate-300 leading-relaxed">
                    <span class="text-amber-400 font-bold block mb-1">Tóm tắt mô hình:</span>
                    {{ $trade->description }}
                </div>
            @endif

            <div class="text-sm text-slate-200 leading-relaxed whitespace-pre-line font-sans space-y-3">
                {{ $trade->analysis ?? $trade->explanation ?? "1. Khung thời gian lớn (H4): Giá đang trong xu hướng tăng rõ rệt với các đỉnh đáy cao dần. Giá chạm lại vùng Demand cực trị sau một đợt thoái lui.\n\n2. Khung xác nhận (M15): Thị trường quét thanh khoản đáy phiên Á (Liquidity Sweep) và đóng nến mạnh mẽ đảo chiều tạo mô hình CHoCH.\n\n3. Điểm vào lệnh: Đặt lệnh Buy Limit tại vùng Fair Value Gap (FVG) hợp lưu với mức Fibonacci 0.618.\n\n4. Quản trị lệnh: Cắt lỗ đặt 5 pip dưới râu nến quét thanh khoản. Chốt lời 1/2 tại đỉnh cũ (1.5R) và giữ phần còn lại tới kháng cự D1 (3.0R)." }}
            </div>

            @if(!empty($trade->notes))
                <div class="p-3.5 rounded-lg bg-amber-500/10 border border-amber-500/20 text-xs text-amber-200 leading-relaxed">
                    <span class="font-bold block mb-1">Lưu ý tâm lý & quản trị rủi ro:</span>
                    {{ $trade->notes }}
                </div>
            @endif
        </div>

        <!-- Checklist Criteria (1 col) -->
        <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-6 shadow-xl space-y-4">
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
