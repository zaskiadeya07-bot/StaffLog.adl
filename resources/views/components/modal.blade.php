<div
    id="{{ $id }}"
    class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center"
    x-data="{ open: false }"
    x-show="open"
    x-transition.opacity.duration.300ms
    x-cloak
>
    <div class="bg-white rounded-3xl max-w-md w-full mx-4"
        @click.outside="open = false; $el.closest('[id]').classList.add('hidden'); $el.closest('[id]').classList.remove('flex')"
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <div class="bg-slate-800 p-5 rounded-t-3xl">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold text-white">{{ $title }}</h3>
                <button @click="open = false; $el.closest('[id]').classList.add('hidden'); $el.closest('[id]').classList.remove('flex')"
                    class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            </div>
        </div>
        <div class="p-6">
            {{ $slot }}
        </div>
        @if(!isset($noFooter) || !$noFooter)
        <div class="p-5 border-t border-slate-100 flex justify-end gap-3">
            <button type="button" @click="open = false; $el.closest('[id]').classList.add('hidden'); $el.closest('[id]').classList.remove('flex')"
                class="btn-secondary px-5">
                {{ $cancelText ?? 'Batal' }}
            </button>
            {{ $footer ?? '' }}
        </div>
        @endif
    </div>
</div>
