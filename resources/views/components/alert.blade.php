<div
    x-data="{ show: true }"
    x-show="show"
    x-transition
    @if(isset($dismissible) && $dismissible)
        x-init="setTimeout(() => show = false, 5000)"
    @endif
    class="flex items-center gap-2 rounded-xl px-4 py-3 mb-4 text-sm
        @if($type === 'success') bg-emerald-50 border border-emerald-200 text-emerald-700
        @elseif($type === 'error') bg-red-50 border border-red-200 text-red-600
        @elseif($type === 'warning') bg-amber-50 border border-amber-200 text-amber-700
        @else bg-blue-50 border border-blue-200 text-blue-700 @endif"
>
    <i class="bi
        @if($type === 'success') bi-check-circle-fill text-emerald-500
        @elseif($type === 'error') bi-exclamation-triangle-fill text-red-500
        @elseif($type === 'warning') bi-exclamation-circle text-amber-500
        @else bi-info-circle text-blue-500 @endif">
    </i>
    <span class="flex-1">{{ $slot }}</span>
    @if(isset($dismissible) && $dismissible)
        <button @click="show = false" class="ml-auto hover:opacity-70">
            <i class="bi bi-x-lg text-xs"></i>
        </button>
    @endif
</div>
