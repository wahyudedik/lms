@php
    $school = \App\Models\School::active()->first();
    $logoUrl = $school ? $school->logo_url : asset('images/icons/icon-192x192.png');
    $appName = $school ? $school->name : config('app.name');
@endphp

<img src="{{ $logoUrl }}" alt="{{ $appName }}" {{ $attributes->merge(['class' => 'h-10 w-auto']) }}>
