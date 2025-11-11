@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-blue-600 text-start text-sm font-medium text-blue-700 bg-blue-50 focus:outline-none focus:text-blue-800 focus:bg-blue-100 focus:border-blue-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2.5 border-l-4 border-transparent text-start text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-900 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
