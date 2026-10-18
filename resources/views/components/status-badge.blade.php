@props([
    'status' => 'pending',
    'label' => null,
])

@php
    $map = [
        'pending' => [
            'bg' => 'bg-amber-500/10',
            'text' => 'text-amber-400',
            'border' => 'border-amber-500/30',
            'dot' => 'bg-amber-400',
            'default_label' => 'Chờ chấm',
        ],
        'reviewed' => [
            'bg' => 'bg-sky-500/10',
            'text' => 'text-sky-400',
            'border' => 'border-sky-500/30',
            'dot' => 'bg-sky-400',
            'default_label' => 'Đã chấm',
        ],
        'approved' => [
            'bg' => 'bg-emerald-500/10',
            'text' => 'text-emerald-400',
            'border' => 'border-emerald-500/30',
            'dot' => 'bg-emerald-400',
            'default_label' => 'Đã duyệt',
        ],
        'revision_needed' => [
            'bg' => 'bg-rose-500/10',
            'text' => 'text-rose-400',
            'border' => 'border-rose-500/30',
            'dot' => 'bg-rose-400',
            'default_label' => 'Cần sửa lại',
        ],
        'submitted' => [
            'bg' => 'bg-indigo-500/10',
            'text' => 'text-indigo-400',
            'border' => 'border-indigo-500/30',
            'dot' => 'bg-indigo-400',
            'default_label' => 'Đã nộp',
        ],
        'active' => [
            'bg' => 'bg-emerald-500/10',
            'text' => 'text-emerald-400',
            'border' => 'border-emerald-500/30',
            'dot' => 'bg-emerald-400',
            'default_label' => 'Hoạt động',
        ],
        'inactive' => [
            'bg' => 'bg-slate-700/50',
            'text' => 'text-slate-400',
            'border' => 'border-slate-600/50',
            'dot' => 'bg-slate-400',
            'default_label' => 'Khóa',
        ],
    ];

    $config = $map[$status] ?? [
        'bg' => 'bg-slate-700/50',
        'text' => 'text-slate-400',
        'border' => 'border-slate-600/50',
        'dot' => 'bg-slate-400',
        'default_label' => ucfirst($status),
    ];

    $displayLabel = $label ?? $config['default_label'];
@endphp

<span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $config['bg'] }} {{ $config['text'] }} {{ $config['border'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $config['dot'] }}"></span>
    <span>{{ $displayLabel }}</span>
</span>
