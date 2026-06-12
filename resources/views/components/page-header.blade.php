<div class="flex justify-between items-center flex-wrap gap-3 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-800">{{ $title }}</h1>
        @if(isset($description))
            <p class="text-slate-500 text-sm">{{ $description }}</p>
        @endif
    </div>
    @if(isset($actionUrl) && isset($actionLabel))
        <a href="{{ $actionUrl }}" class="btn-primary inline-flex items-center gap-2">
            @if(isset($actionIcon))<i class="bi {{ $actionIcon }}"></i>@endif
            {{ $actionLabel }}
        </a>
    @endif
    @if(isset($actionSlot))
        {{ $actionSlot }}
    @endif
</div>
