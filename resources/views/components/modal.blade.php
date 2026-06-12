<div
    id="{{ $id }}"
    class="fixed inset-0 bg-black/50 z-50 hidden items-center justify-center"
    x-data="{ open: false }"
    x-show="open"
    x-cloak
>
    <div class="bg-white rounded-3xl max-w-md w-full mx-4">
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
