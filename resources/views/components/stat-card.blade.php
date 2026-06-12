<div class="card p-5 flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl
        @php
            $colors = [
                'emerald' => 'bg-emerald-100 text-emerald-600',
                'amber' => 'bg-amber-100 text-amber-600',
                'blue' => 'bg-blue-100 text-blue-600',
                'slate' => 'bg-slate-100 text-slate-600',
                'red' => 'bg-red-100 text-red-600',
            ];
        @endphp
        {{ $colors[$color] ?? 'bg-slate-100 text-slate-600' }}
    ">
        <i class="bi {{ $icon }}"></i>
    </div>
    <div>
        <p class="text-2xl font-bold text-slate-800">{{ $value }}</p>
        <p class="text-xs text-slate-500">{{ $label }}</p>
    </div>
</div>
