@php
    $statusName = $status ?? 'Hadir';
    $normalizedStatus = strtolower($statusName);

    $statusStyles = [
        'hadir' => 'bg-emerald-100 text-emerald-700',
        'izin' => 'bg-amber-100 text-amber-700',
        'sakit' => 'bg-sky-100 text-sky-700',
        'alpha' => 'bg-rose-100 text-rose-700',
    ];

    $badgeStyle = $statusStyles[$normalizedStatus] ?? 'bg-slate-100 text-slate-700';
@endphp

<span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badgeStyle }}">
    {{ $statusName }}
</span>
