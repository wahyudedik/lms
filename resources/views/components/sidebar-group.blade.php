@props(['label', 'icon', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }" class="space-y-0.5">
    <button type="button"
        class="group w-full flex items-center justify-between px-3 py-2 rounded-lg text-sm font-medium transition-all duration-200 select-none mx-2 w-[calc(100%-16px)]
        {{ $active ? 'text-indigo-600 bg-indigo-50' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }}"
        @click="open = !open">
        <span class="flex items-center gap-3">
            <div class="flex items-center justify-center w-5 h-5">
                <i
                    class="{{ $icon }} text-sm transition-colors duration-200 {{ $active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500' }}"></i>
            </div>
            <span>{{ $label }}</span>
        </span>
        <svg class="h-4 w-4 text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }"
            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" x-cloak
        class="space-y-0.5 relative before:absolute before:left-[26px] before:top-0 before:h-full before:w-px before:bg-gray-200">
        {{ $slot }}
    </div>
</div>
