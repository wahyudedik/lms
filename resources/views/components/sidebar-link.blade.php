@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-3 px-3 py-2 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-lg group transition-all duration-200 mx-2'
    : 'flex items-center gap-3 px-3 py-2 text-sm font-medium text-gray-600 rounded-lg hover:text-gray-900 hover:bg-gray-50 group transition-all duration-200 mx-2';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
