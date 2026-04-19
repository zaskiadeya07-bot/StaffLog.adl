@php
    $accent = $accent ?? 'blue';
    $accentStyles = [
        'blue' => 'border-blue-200 bg-blue-50 text-blue-800',
        'green' => 'border-emerald-200 bg-emerald-50 text-emerald-800',
        'amber' => 'border-amber-200 bg-amber-50 text-amber-800',
    ];
    $cardStyle = $accentStyles[$accent] ?? $accentStyles['blue'];
@endphp

<div class="rounded-2xl border p-5 shadow-sm {{ $cardStyle }}">
    <p class="text-xs font-semibold uppercase tracking-[0.2em]">{{ $label }}</p>
    <p class="mt-3 text-3xl font-extrabold">{{ $value }}</p>
</div>
