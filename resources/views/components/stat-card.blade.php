@props([
    'title' => '',
    'value' => '0',
    'change' => null,
    'changeType' => 'positive', // 'positive', 'negative', 'neutral'
    'color' => 'emerald', // 'emerald', 'rose', 'sky', 'amber', 'purple', 'indigo'
    'subtitle' => null,
])

@php
    $colorMap = [
        'emerald' => [
            'bg' => 'bg-emerald-500/10',
            'border' => 'border-emerald-500/20',
            'text' => 'text-emerald-400',
            'glow' => 'group-hover:border-emerald-500/40',
        ],
        'rose' => [
            'bg' => 'bg-rose-500/10',
            'border' => 'border-rose-500/20',
            'text' => 'text-rose-400',
            'glow' => 'group-hover:border-rose-500/40',
        ],
        'sky' => [
            'bg' => 'bg-sky-500/10',
            'border' => 'border-sky-500/20',
            'text' => 'text-sky-400',
            'glow' => 'group-hover:border-sky-500/40',
        ],
        'amber' => [
            'bg' => 'bg-amber-500/10',
            'border' => 'border-amber-500/20',
            'text' => 'text-amber-400',
            'glow' => 'group-hover:border-amber-500/40',
        ],
        'purple' => [
            'bg' => 'bg-purple-500/10',
            'border' => 'border-purple-500/20',
            'text' => 'text-purple-400',
            'glow' => 'group-hover:border-purple-500/40',
        ],
        'indigo' => [
            'bg' => 'bg-indigo-500/10',
            'border' => 'border-indigo-500/20',
            'text' => 'text-indigo-400',
            'glow' => 'group-hover:border-indigo-500/40',
        ],
    ];

    $styles = $colorMap[$color] ?? $colorMap['emerald'];
@endphp

<div class="group relative overflow-hidden rounded-xl bg-slate-800/90 border border-slate-700/80 p-5 shadow-lg backdrop-blur transition-all duration-300 hover:shadow-xl hover:translate-y-[-2px] {{ $styles['glow'] }}">
    <!-- Background subtle gradient glow -->
    <div class="absolute -right-6 -bottom-6 w-24 h-24 rounded-full {{ $styles['bg'] }} blur-2xl pointer-events-none transition-all group-hover:scale-125"></div>

    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <p class="text-xs font-medium uppercase tracking-wider text-slate-400 truncate">{{ $title }}</p>
            <h3 class="mt-2 text-2xl font-bold tracking-tight text-white font-mono">{{ $value }}</h3>
            
            @if($change !== null || $subtitle !== null)
                <div class="mt-2 flex items-center gap-1.5 text-xs">
                    @if($change !== null)
                        @if($changeType === 'positive')
                            <span class="inline-flex items-center text-emerald-400 font-medium">
                                <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                {{ $change }}
                            </span>
                        @elseif($changeType === 'negative')
                            <span class="inline-flex items-center text-rose-400 font-medium">
                                <svg class="w-3.5 h-3.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                                {{ $change }}
                            </span>
                        @else
                            <span class="inline-flex items-center text-slate-400 font-medium">
                                {{ $change }}
                            </span>
                        @endif
                    @endif

                    @if($subtitle)
                        <span class="text-slate-500 truncate">{{ $subtitle }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="p-3 rounded-lg {{ $styles['bg'] }} {{ $styles['text'] }} border {{ $styles['border'] }} shrink-0">
            @if(isset($icon))
                {{ $icon }}
            @else
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            @endif
        </div>
    </div>
</div>
