<span class="inline-flex items-center text-xs font-medium px-2.5 py-1 rounded-full
    @php
        $colors = [
            'aktif' => 'bg-emerald-100 text-emerald-700',
            'nonaktif' => 'bg-red-100 text-red-700',
            'hadir' => 'bg-emerald-100 text-emerald-700',
            'terlambat' => 'bg-amber-100 text-amber-700',
            'izin' => 'bg-blue-100 text-blue-700',
            'alpha' => 'bg-slate-100 text-slate-600',
            'pending' => 'bg-yellow-100 text-yellow-700',
            'disetujui' => 'bg-emerald-100 text-emerald-700',
            'ditolak' => 'bg-red-100 text-red-700',
            'dibatalkan' => 'bg-slate-100 text-slate-500',
        ];
    @endphp
    {{ $colors[$type] ?? 'bg-slate-100 text-slate-600' }}
">
    {{ $slot }}
</span>
