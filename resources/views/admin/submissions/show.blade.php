@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-4 border-b border-slate-800">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 mb-1">
                <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" class="hover:text-white">Bài nộp</a>
                <span>/</span>
                <span class="text-emerald-400">Chi tiết bài làm</span>
            </div>
            <h1 class="text-2xl font-bold tracking-tight text-white flex items-center gap-3">
                <span>{{ $submission->assignment->title ?? 'Bài tập thực hành' }}</span>
                <x-status-badge :status="$submission->status ?? 'pending'" />
            </h1>
            <p class="text-sm text-slate-400 mt-1">
                Học viên: <span class="text-emerald-400 font-semibold">{{ $submission->user->name ?? 'Nguyễn Văn Minh' }}</span> ({{ $submission->user->email ?? 'minh@example.com' }}) • Nộp lúc: {{ isset($submission->created_at) ? $submission->created_at->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', $submission->id ?? 1) : url('/admin/submissions/' . ($submission->id ?? 1) . '/review') }}" 
               class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-lg shadow-emerald-600/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Chấm điểm bài này
            </a>
            <a href="{{ Route::has('admin.submissions.index') ? route('admin.submissions.index') : url('/admin/submissions') }}" 
               class="inline-flex items-center gap-2 px-3.5 py-2 rounded-lg text-xs font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 transition-colors">
                Quay lại
            </a>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Submission Details & Trade Data (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Content Card -->
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <span class="text-sky-400">📝</span>
                    Nội dung giải thích & Phân tích của học viên
                </h3>
                <div class="p-4 rounded-lg bg-slate-900 border border-slate-800 text-sm text-slate-200 leading-relaxed whitespace-pre-line">
                    {{ $submission->content ?? 'Em đã xác định vùng Demand H4 của cặp EURUSD sau một nhịp Liquidity Sweep tại đáy Á. Khi giá hồi về vùng FVG với nến Pin Bar từ chối giá mạnh, em vào lệnh Buy với SL dưới râu nến và TP tại đỉnh Swing High trước đó. Tỷ lệ RR đạt 1:2.8.' }}
                </div>
            </div>

            <!-- Trade Data Display (If Available) -->
            @php
                $tradeData = $submission->trade_data ?? [
                    'pair' => 'EURUSD',
                    'direction' => 'BUY',
                    'timeframe' => 'H4',
                    'entry_price' => '1.08450',
                    'stop_loss' => '1.08200',
                    'take_profit' => '1.09150',
                    'risk_reward' => '1:2.8',
                    'pnl_pips' => '+70 pips',
                    'result' => 'win',
                ];
            @endphp
            @if(!empty($tradeData))
                <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                    <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                        <span class="text-emerald-400">💹</span>
                        Thông số lệnh giao dịch (Trade Data)
                    </h3>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs font-mono">
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Cặp tiền</span>
                            <span class="font-bold text-white text-sm">{{ $tradeData['pair'] ?? 'EURUSD' }}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Vị thế</span>
                            <span class="font-bold text-sm {{ strtolower($tradeData['direction'] ?? 'buy') === 'buy' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ strtoupper($tradeData['direction'] ?? 'BUY') }}
                            </span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Entry Price</span>
                            <span class="font-bold text-white text-sm">{{ $tradeData['entry_price'] ?? '1.08450' }}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Risk : Reward</span>
                            <span class="font-bold text-sky-400 text-sm">{{ $tradeData['risk_reward'] ?? '1:2.8' }}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Stop Loss (SL)</span>
                            <span class="font-bold text-rose-400 text-sm">{{ $tradeData['stop_loss'] ?? '1.08200' }}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Take Profit (TP)</span>
                            <span class="font-bold text-emerald-400 text-sm">{{ $tradeData['take_profit'] ?? '1.09150' }}</span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">Kết quả</span>
                            <span class="mt-1 block">
                                <x-trade-result-badge :result="$tradeData['result'] ?? 'win'" />
                            </span>
                        </div>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800">
                            <span class="text-slate-400 block text-[10px] uppercase font-sans">P&L Pips</span>
                            <span class="font-bold text-emerald-400 text-sm">{{ $tradeData['pnl_pips'] ?? '+70' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Screenshots / Attachments -->
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                <h3 class="text-sm font-bold text-white mb-3 flex items-center gap-2">
                    <span class="text-amber-400">🖼️</span>
                    Ảnh chụp biểu đồ & File đính kèm
                </h3>
                <div class="space-y-3">
                    @if(!empty($submission->attachments) || !empty($submission->file_path))
                        <div class="rounded-lg overflow-hidden border border-slate-700 bg-slate-950">
                            <img src="{{ asset('storage/' . ($submission->file_path ?? 'sample-chart.png')) }}" alt="Biểu đồ phân tích" class="w-full max-h-96 object-contain">
                        </div>
                    @else
                        <!-- Visual placeholder trading chart mockup -->
                        <div class="p-8 text-center rounded-lg bg-slate-900 border border-slate-800">
                            <div class="w-16 h-16 mx-auto mb-3 rounded-full bg-slate-800 flex items-center justify-center text-slate-500">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-sm font-semibold text-slate-300">Biểu đồ phân tích TradingView đã đính kèm</p>
                            <a href="#" class="mt-2 inline-flex items-center gap-1.5 text-xs text-sky-400 hover:text-sky-300 font-mono">
                                <span>EURUSD_H4_Setup_Liquidity_Sweep.png</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review & Feedback Sidebar (1 col) -->
        <div class="space-y-6">
            <!-- Review Summary Card -->
            <div class="rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg">
                <h3 class="text-sm font-bold text-white pb-3 border-b border-slate-700/60 mb-4">
                    Kết Quả Đánh Giá Của Admin
                </h3>

                <div class="space-y-4">
                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Trạng thái duyệt:</span>
                        <x-status-badge :status="$submission->status ?? 'approved'" />
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Điểm số:</span>
                        <div class="flex items-baseline gap-1">
                            <span class="text-3xl font-extrabold font-mono text-emerald-400">{{ $submission->score ?? '9.0' }}</span>
                            <span class="text-sm text-slate-400">/ 10</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-xs text-slate-400 block mb-1">Nhận xét của giảng viên:</span>
                        <div class="p-3 rounded-lg bg-slate-900 border border-slate-800 text-xs text-slate-300 leading-relaxed italic">
                            "{{ $submission->feedback ?? 'Rất tốt! Phân tích cấu trúc thị trường chuẩn xác, bắt đúng nhịp quét thanh khoản Liquidity Sweep. Điểm vào lệnh an toàn và tuân thủ chặt chẽ nguyên tắc tỷ lệ RR tối thiểu 1:2.' }}"
                        </div>
                    </div>

                    @if(!empty($submission->corrections))
                        <div>
                            <span class="text-xs text-rose-400 block mb-1 font-semibold">Gợi ý cần cải thiện:</span>
                            <div class="p-3 rounded-lg bg-rose-500/10 border border-rose-500/20 text-xs text-rose-300 leading-relaxed">
                                {{ $submission->corrections }}
                            </div>
                        </div>
                    @endif

                    <!-- Featured Status & Toggle -->
                    <div class="p-3.5 rounded-lg {{ $submission->is_featured ? 'bg-amber-950/40 border border-amber-500/50 text-amber-300' : 'bg-slate-900 border border-slate-800 text-slate-400' }} space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold flex items-center gap-1.5 {{ $submission->is_featured ? 'text-amber-400' : 'text-slate-400' }}">
                                ⭐ {{ $submission->is_featured ? 'Bài tập tiêu biểu' : 'Chưa gắn nhãn tiêu biểu' }}
                            </span>
                            @if($submission->is_featured)
                                <span class="text-[10px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    Showcase
                                </span>
                            @endif
                        </div>
                        @if($submission->is_featured && $submission->featured_note)
                            <p class="text-[11px] text-amber-200/90 italic">
                                "{{ $submission->featured_note }}"
                            </p>
                        @endif

                        <form method="POST" action="{{ route('admin.submissions.toggle-featured', $submission->id) }}" class="pt-1">
                            @csrf
                            <button type="submit" 
                                    class="w-full py-1.5 px-3 rounded text-xs font-semibold {{ $submission->is_featured ? 'bg-slate-800 hover:bg-rose-950/50 hover:text-rose-400 text-slate-300 border border-slate-700' : 'bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40' }} transition">
                                {{ $submission->is_featured ? 'Hủy chọn Tiêu Biểu' : '⭐ Đặt làm Bài Tập Tiêu Biểu' }}
                            </button>
                        </form>
                    </div>

                    <div class="pt-4 border-t border-slate-700/60">
                        <a href="{{ Route::has('admin.submissions.review') ? route('admin.submissions.review', $submission->id ?? 1) : url('/admin/submissions/' . ($submission->id ?? 1) . '/review') }}" 
                           class="w-full flex items-center justify-center gap-2 py-2.5 rounded-lg text-xs font-semibold bg-emerald-600 hover:bg-emerald-500 text-white shadow-md shadow-emerald-600/20 transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Chấm lại hoặc Sửa đánh giá
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
