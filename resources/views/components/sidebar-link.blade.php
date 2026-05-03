@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-3 px-4 py-2.5 text-sm font-semibold text-blue-600 bg-blue-50 rounded-lg group transition-all duration-150 mx-2 min-w-0 w-[calc(100%-16px)]'
    : 'flex items-center gap-3 px-4 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:text-gray-900 hover:bg-gray-100 group transition-all duration-150 mx-2 min-w-0 w-[calc(100%-16px)]';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
