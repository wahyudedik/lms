@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 focus:ring-2 transition duration-150 ease-in-out disabled:bg-gray-100 disabled:cursor-not-allowed']) }}>
