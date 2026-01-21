@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>{{ __('Laporan Hasil Belajar') }}
                </h1>
                <p class="text-gray-600">{{ __('Ringkasan performa dan nilai ujian Anda') }}</p>
            </div>
            <a href="{{ route('siswa.reports.my-transcript') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow flex items-center">
                <i class="fas fa-file-alt mr-2"></i>{{ __('Lihat Transkrip Resmi') }}
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">{{ __('Total Ujian') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $statistics['total_exams'] }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full text-blue-600">
                        <i class="fas fa-clipboard-list text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ $statistics['completed'] }} selesai</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">{{ __('Lulus') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $statistics['pass_count'] }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full text-green-600">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">Rate: {{ number_format($statistics['pass_rate'], 1) }}%</p>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">{{ __('Gagal') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ $statistics['fail_count'] }}</p>
                    </div>
                    <div class="p-3 bg-red-100 rounded-full text-red-600">
                        <i class="fas fa-times-circle text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-semibold uppercase">{{ __('Rata-rata Nilai') }}</p>
                        <p class="text-3xl font-bold text-gray-800">{{ number_format($statistics['average_score'], 1) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full text-purple-600">
                        <i class="fas fa-star text-xl"></i>
                    </div>
                </div>
                <div class="flex justify-between text-xs text-gray-500 mt-2">
                    <span>Min: {{ number_format($statistics['lowest_score'], 1) }}</span>
                    <span>Max: {{ number_format($statistics['highest_score'], 1) }}</span>
                </div>
            </div>
        </div>

        <!-- Recent Attempts -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden mb-8">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-history text-indigo-600 mr-2"></i>{{ __('Riwayat Ujian Terakhir') }}
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Kursus') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Ujian') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nilai') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tanggal') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Aksi') }}</th>
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
                                    <span class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                        {{ number_format($attempt->score, 2) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $attempt->passed ? 'LULUS' : 'GAGAL' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-700">
                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('d/m/Y H:i') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                    <a href="{{ route('siswa.exams.review-attempt', $attempt->id) }}" class="text-indigo-600 hover:text-indigo-900 font-medium">
                                        {{ __('Review') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                    {{ __('Belum ada riwayat ujian.') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Course Performance -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-book text-indigo-600 mr-2"></i>{{ __('Performa per Kursus') }}
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($attemptsByCourse as $courseStats)
                    <div class="border rounded-lg p-4 hover:shadow-md transition">
                        <h4 class="font-bold text-gray-800 mb-2 truncate" title="{{ $courseStats['course']->title }}">
                            {{ $courseStats['course']->title }}
                        </h4>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Ujian Diikuti:</span>
                                <span class="font-semibold">{{ $courseStats['total'] }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Rata-rata Nilai:</span>
                                <span class="font-semibold {{ $courseStats['average'] >= 70 ? 'text-green-600' : 'text-yellow-600' }}">
                                    {{ number_format($courseStats['average'], 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Lulus:</span>
                                <span class="font-semibold text-green-600">{{ $courseStats['passed'] }}</span>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t text-center">
                            <a href="{{ route('siswa.reports.my-transcript', ['course_id' => $courseStats['course']->id]) }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                                {{ __('Lihat Detail') }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-8 text-gray-500">
                        {{ __('Belum ada data kursus.') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection