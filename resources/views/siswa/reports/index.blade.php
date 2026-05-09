<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>{{ __('Laporan Hasil Belajar') }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ __('Ringkasan performa dan nilai ujian Anda') }}</p>
            </div>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.reports.my-transcript') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-sm transition">
                <i class="fas fa-file-alt"></i>{{ __('Lihat Transkrip Resmi') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">{{ __('Total Ujian') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $statistics['total_exams'] }}</p>
                            <p class="text-xs text-gray-500 mt-2">{{ $statistics['completed'] }} selesai</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-clipboard-list text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">{{ __('Lulus') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $statistics['pass_count'] }}</p>
                            <p class="text-xs text-gray-500 mt-2">Rate:
                                {{ number_format($statistics['pass_rate'], 1) }}%</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-red-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">{{ __('Gagal') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $statistics['fail_count'] }}</p>
                        </div>
                        <div class="bg-red-100 rounded-full p-3">
                            <i class="fas fa-times-circle text-red-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-purple-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">{{ __('Rata-rata Nilai') }}</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($statistics['average_score'], 1) }}</p>
                            <div class="flex justify-between text-xs text-gray-500 mt-2">
                                <span>Min: {{ number_format($statistics['lowest_score'], 1) }}</span>
                                <span>Max: {{ number_format($statistics['highest_score'], 1) }}</span>
                            </div>
                        </div>
                        <div class="bg-purple-100 rounded-full p-3">
                            <i class="fas fa-star text-purple-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Attempts -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-history text-indigo-600 mr-2"></i>{{ __('Riwayat Ujian Terakhir') }}
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full border-collapse">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                    {{ __('Kursus') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                    {{ __('Ujian') }}</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                    {{ __('Nilai') }}</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                    {{ __('Status') }}</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                    {{ __('Tanggal') }}</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase border-b">
                                    {{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($attempts->take(5) as $attempt)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $attempt->exam->course->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        <div class="font-semibold">{{ $attempt->exam->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($attempt->score, 2) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="inline-flex items-center gap-1 px-3 py-1 text-xs font-semibold rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            <i class="fas fa-{{ $attempt->passed ? 'check' : 'times' }}-circle"></i>
                                            {{ $attempt->passed ? 'LULUS' : 'GAGAL' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-700">
                                        {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.review-attempt', $attempt->id) }}"
                                            class="text-indigo-600 hover:text-indigo-900 font-medium">
                                            <i class="fas fa-eye mr-1"></i>{{ __('Review') }}
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-10 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div
                                                class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-3">
                                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                            </div>
                                            <p class="text-gray-500">{{ __('Belum ada riwayat ujian.') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Course Performance -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-book text-indigo-600 mr-2"></i>{{ __('Performa per Kursus') }}
                    </h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($attemptsByCourse as $courseStats)
                            <div class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                <h4 class="font-bold text-gray-900 mb-3 truncate"
                                    title="{{ $courseStats['course']->title }}">
                                    <i
                                        class="fas fa-book-open text-indigo-600 mr-2"></i>{{ $courseStats['course']->title }}
                                </h4>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Ujian Diikuti:</span>
                                        <span class="font-semibold text-gray-900">{{ $courseStats['total'] }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Rata-rata Nilai:</span>
                                        <span
                                            class="font-semibold {{ $courseStats['average'] >= 70 ? 'text-green-600' : 'text-yellow-600' }}">
                                            {{ number_format($courseStats['average'], 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Lulus:</span>
                                        <span class="font-semibold text-green-600">{{ $courseStats['passed'] }}</span>
                                    </div>
                                </div>
                                <div class="mt-4 pt-3 border-t text-center">
                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.reports.my-transcript', ['course_id' => $courseStats['course']->id]) }}"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                        <i class="fas fa-arrow-right mr-1"></i>{{ __('Lihat Detail') }}
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500">{{ __('Belum ada data kursus.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
