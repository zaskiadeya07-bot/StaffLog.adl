@php
    $totalCount = max((int) ($total ?? 0), 1);
    $percentage = (int) round(((int) $value / $totalCount) * 100);

    $themeByKey = [
        'H' => [
            'badge' => 'bg-blue-100 text-blue-700',
            'text' => 'text-blue-700',
            'track' => 'bg-blue-100',
            'bar' => 'bg-blue-600',
        ],
        'S' => [
            'badge' => 'bg-sky-100 text-sky-700',
            'text' => 'text-sky-700',
            'track' => 'bg-sky-100',
            'bar' => 'bg-sky-500',
        ],
        'I' => [
            'badge' => 'bg-amber-100 text-amber-700',
            'text' => 'text-amber-700',
            'track' => 'bg-amber-100',
            'bar' => 'bg-amber-500',
        ],
        'A' => [
            'badge' => 'bg-rose-100 text-rose-700',
            'text' => 'text-rose-700',
            'track' => 'bg-rose-100',
            'bar' => 'bg-rose-500',
        ],
    ];

    $theme = $themeByKey[$key] ?? $themeByKey['H'];
@endphp

<div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-full text-lg font-black {{ $theme['badge'] }}">
                {{ $key }}
            </span>
            <div>
                <p class="text-sm font-semibold text-slate-700">{{ $description }}</p>
                <p class="text-xs font-semibold {{ $theme['text'] }}">{{ $percentage }}%</p>
            </div>
        </div>
        <span class="text-3xl font-black text-slate-900">{{ $value }}</span>
    </div>

    <div class="mt-4 h-2 w-full overflow-hidden rounded-full {{ $theme['track'] }}">
        <div class="h-full rounded-full {{ $theme['bar'] }}" style="width: {{ $percentage }}%;"></div>
    </div>
</div>
