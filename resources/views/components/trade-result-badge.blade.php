@props([
    'result' => 'pending',
    'pips' => null,
    'amount' => null,
])

@php
    $res = strtolower((string)$result);
    $map = [
        'win' => [
            'bg' => 'bg-emerald-500/15',
            'text' => 'text-emerald-400',
            'border' => 'border-emerald-500/30',
            'icon' => 'M5 10l7-7m0 0l7 7m-7-7v18',
            'label' => 'WIN',
        ],
        'loss' => [
            'bg' => 'bg-rose-500/15',
            'text' => 'text-rose-400',
            'border' => 'border-rose-500/30',
            'icon' => 'M19 14l-7 7m0 0l-7-7m7 7V3',
            'label' => 'LOSS',
        ],
        'breakeven' => [
            'bg' => 'bg-amber-500/15',
            'text' => 'text-amber-400',
            'border' => 'border-amber-500/30',
            'icon' => 'M5 12h14',
            'label' => 'BE',
        ],
        'pending' => [
            'bg' => 'bg-slate-700/60',
            'text' => 'text-slate-300',
            'border' => 'border-slate-600/60',
            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
            'label' => 'OPEN',
        ],
    ];

    $cfg = $map[$res] ?? $map['pending'];
@endphp

<span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded text-xs font-semibold font-mono tracking-wide border {{ $cfg['bg'] }} {{ $cfg['text'] }} {{ $cfg['border'] }}">
    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $cfg['icon'] }}"></path>
    </svg>
    <span>{{ $cfg['label'] }}</span>

    @if($pips !== null)
        <span class="opacity-80 text-[11px] font-normal">({{ ($pips > 0 ? '+' : '') . $pips }}p)</span>
    @elseif($amount !== null)
        <span class="opacity-80 text-[11px] font-normal">({{ ($amount > 0 ? '+' : '') . number_format($amount, 2) }}$)</span>
    @endif
</span>
