@props(['active' => false])

@php
$classes = ($active ?? false)
    ? 'flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-indigo-600 bg-indigo-50 rounded-lg border border-indigo-100 shadow-sm'
    : 'flex items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-600 rounded-lg border border-transparent hover:text-gray-900 hover:bg-gray-50 hover:border-gray-200 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>

