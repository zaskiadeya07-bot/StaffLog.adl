@php
    $isActive = $active ?? false;
    $iconName = $icon ?? 'default';
    $iconMap = [
        'rekap' => 'ri-bar-chart-box-line',
        'tambah' => 'ri-user-add-line',
        'profil' => 'ri-user-3-line',
        'checkin' => 'ri-login-box-line',
        'checkout' => 'ri-logout-box-line',
        'riwayat' => 'ri-time-line',
        'default' => 'ri-checkbox-blank-circle-line',
    ];

    $iconClass = $iconMap[$iconName] ?? $iconMap['default'];
@endphp

<a
    href="{{ $href }}"
    class="group flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition {{ $isActive ? 'bg-blue-600 text-white shadow-lg shadow-blue-600/30' : 'text-slate-600 hover:bg-blue-50 hover:text-blue-700' }}"
>
    <span class="inline-flex h-8 w-8 items-center justify-center rounded-lg {{ $isActive ? 'bg-white/20 text-white' : 'bg-blue-100 text-blue-600 group-hover:bg-blue-200' }}">
        <i class="{{ $iconClass }} text-lg leading-none" aria-hidden="true"></i>
    </span>
    <span>{{ $label }}</span>
</a>
