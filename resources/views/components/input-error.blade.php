@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'mt-1.5 text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex items-center">
                <i class="fas fa-exclamation-circle mr-1.5 text-xs"></i>{{ $message }}
            </li>
        @endforeach
    </ul>
@endif
