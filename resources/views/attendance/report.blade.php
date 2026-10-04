<x-app-layout>
    @php
        $exportParams = array_filter(
            [
                'material_id' => $filters['material_id'] ?? null,
                'date_from' => $filters['date_from'] ?? null,
                'date_to' => $filters['date_to'] ?? null,
            ],
            fn($value) => $value !== null && $value !== '',
        );
    @endphp
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-chart-bar mr-2 text-teal-600"></i>{{ __('Report Absensi') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $course->title }} · {{ __('Kode') }}: {{ $course->code }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.attendance.export', array_merge(['course' => $course], $exportParams)) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-file-excel"></i>
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">{{ __('Back') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Filter -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
                <form method="GET"
                    action="{{ route(auth()->user()->getRolePrefix() . '.attendance.report', $course) }}"
                    class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="material_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-book text-gray-400 mr-1"></i>{{ __('Materi') }}
                        </label>
                        <select name="material_id" id="material_id"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                            <option value="">{{ __('Semua Materi') }}</option>
                            @foreach ($materials as $material)
                                <option value="{{ $material->id }}"
                                    {{ ($filters['material_id'] ?? null) == $material->id ? 'selected' : '' }}>
                                    {{ $material->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="date_from" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>{{ __('Dari Tanggal') }}
                        </label>
                        <input type="date" name="date_from" id="date_from" value="{{ $filters['date_from'] ?? '' }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="date_to" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>{{ __('Sampai Tanggal') }}
                        </label>
                        <input type="date" name="date_to" id="date_to" value="{{ $filters['date_to'] ?? '' }}"
                            class="block w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent">
                    </div>
                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-search"></i>{{ __('Filter') }}
                        </button>
                        @if (($filters['material_id'] ?? null) || ($filters['date_from'] ?? null) || ($filters['date_to'] ?? null))
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.attendance.report', $course) }}"
                                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg shadow-sm transition">
                                <i class="fas fa-times"></i>{{ __('Reset') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Ringkasan -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-5 border-l-4 border-indigo-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">{{ __('Total') }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['total'] }}</p>
                        </div>
                        <div class="bg-indigo-100 rounded-full p-3">
                            <i class="fas fa-clipboard-list text-indigo-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-5 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">{{ __('Hadir') }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['hadir'] }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-5 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">{{ __('Sakit') }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['sakit'] }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-notes-medical text-yellow-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-5 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">{{ __('Ijin') }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['ijin'] }}</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-envelope-open-text text-blue-600 text-lg"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-5 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs text-gray-600 font-semibold uppercase">{{ __('Keluar') }}</p>
                            <p class="text-2xl font-bold text-gray-900 mt-1">{{ $summary['keluar'] }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-door-open text-red-600 text-lg"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail per Materi -->
            @php
                $statusBadge = [
                    'hadir' => 'bg-green-100 text-green-800',
                    'sakit' => 'bg-yellow-100 text-yellow-800',
                    'ijin' => 'bg-blue-100 text-blue-800',
                    'keluar' => 'bg-red-100 text-red-800',
                ];
            @endphp

            @if ($attendances->isNotEmpty())
                @foreach ($materials as $material)
                    @if (!$attendances->has($material->id))
                        @continue
                    @endif
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div
                            class="px-4 sm:px-6 py-4 bg-gray-50 border-b border-gray-200 flex flex-wrap items-center justify-between gap-2">
                            <h3 class="text-base font-bold text-gray-900">
                                <i class="fas fa-book-open text-teal-600 mr-2"></i>{{ $material->title }}
                                <span class="text-xs font-semibold text-gray-400 ml-1">#{{ $material->order }}</span>
                            </h3>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800">
                                {{ $attendances->get($material->id)->count() }} {{ __('data absensi') }}
                            </span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-white">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Nama') }}
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Tanggal') }}
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Alasan') }}
                                        </th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Dicatat Oleh') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($attendances->get($material->id) as $attendance)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                {{ $attendance->student?->name ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ $attendance->attendance_date?->format('d M Y') ?? '-' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge[$attendance->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $attendance->status_display }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">
                                                {{ $attendance->alasan ?: '—' }}
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                                                {{ $attendance->recorder?->name ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="bg-white rounded-lg shadow-md border border-gray-200 py-16">
                    <div class="flex flex-col items-center justify-center text-gray-500">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-inbox text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-lg font-semibold mb-2">{{ __('Belum ada data absensi') }}</p>
                        <p class="text-sm text-gray-400">{{ __('Coba ubah filter tanggal atau pilih materi lain.') }}
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
