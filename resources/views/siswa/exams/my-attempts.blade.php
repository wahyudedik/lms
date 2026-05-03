<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-history mr-2"></i>{{ __('My Exam History') }}
            </h2>
            <a href="{{ route('siswa.exams.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-list"></i>
                {{ __('Browse Exams') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if ($attempts->count() > 0)
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Exam') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Waktu
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Durasi
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Score') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($attempts as $attempt)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <!-- Exam Info -->
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $attempt->exam->title }}
                                                </div>
                                                <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                                    <i class="fas fa-book text-blue-500"></i>
                                                    {{ $attempt->exam->course->title }}
                                                </div>
                                            </td>

                                            <!-- Time -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y') : '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('H:i') : '-' }}
                                                </div>
                                            </td>

                                            <!-- Duration -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    @if ($attempt->time_spent)
                                                        {{ floor($attempt->time_spent / 60) }}:{{ str_pad($attempt->time_spent % 60, 2, '0', STR_PAD_LEFT) }}
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    dari {{ $attempt->exam->duration_minutes }} menit
                                                </div>
                                            </td>

                                            <!-- Score -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($attempt->score !== null)
                                                    <div class="flex items-center">
                                                        <div
                                                            class="text-2xl font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                                            {{ number_format($attempt->score, 1) }}%
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ number_format($attempt->total_points_earned, 1) }}/{{ number_format($attempt->total_points_possible, 1) }}
                                                        poin
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>

                                            <!-- Status -->
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex flex-col gap-2">
                                                    {!! $attempt->status_badge !!}

                                                    @if ($attempt->passed === true)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i>Lulus
                                                        </span>
                                                    @elseif ($attempt->passed === false)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            <i class="fas fa-times-circle mr-1"></i>Tidak Lulus
                                                        </span>
                                                    @endif

                                                    @if ($attempt->violations && count($attempt->violations) > 0)
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800"
                                                            title="{{ count($attempt->violations) }} pelanggaran">
                                                            <i
                                                                class="fas fa-exclamation-triangle mr-1"></i>{{ count($attempt->violations) }}
                                                            violations
                                                        </span>
                                                    @endif
                                                </div>
                                            </td>

                                            <!-- Actions -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex gap-2">
                                                    @if ($attempt->status === 'in_progress')
                                                        <a href="{{ route('siswa.exams.take', $attempt) }}"
                                                            class="text-yellow-600 hover:text-yellow-900"
                                                            title="Lanjutkan">
                                                            <i class="fas fa-play"></i>
                                                        </a>
                                                    @else
                                                        <a href="{{ route('siswa.exams.review-attempt', $attempt) }}"
                                                            class="text-blue-600 hover:text-blue-900" title="Review">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    @endif

                                                    <a href="{{ route('siswa.exams.show', $attempt->exam) }}"
                                                        class="text-green-600 hover:text-green-900"
                                                        title="{{ __('Exam Details') }}">
                                                        <i class="fas fa-info-circle"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $attempts->links() }}
                        </div>
                    </div>
                </div>

                <!-- Statistics Summary -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
                    @php
                        $totalAttempts = $attempts->total();
                        $completedAttempts = \App\Models\ExamAttempt::where('user_id', auth()->id())
                            ->whereIn('status', ['completed', 'submitted'])
                            ->count();
                        $passedAttempts = \App\Models\ExamAttempt::where('user_id', auth()->id())
                            ->where('passed', true)
                            ->count();
                        $avgScore = \App\Models\ExamAttempt::where('user_id', auth()->id())
                            ->whereNotNull('score')
                            ->avg('score');
                    @endphp

                    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-gray-600">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-gray-100 rounded-lg mr-4">
                                    <i class="fas fa-clipboard-list text-gray-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600 mb-1">Total Percobaan</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $totalAttempts }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-blue-600">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-lg mr-4">
                                    <i class="fas fa-check-circle text-blue-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600 mb-1">Selesai</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $completedAttempts }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-green-600">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-lg mr-4">
                                    <i class="fas fa-trophy text-green-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600 mb-1">Lulus</div>
                                    <div class="text-2xl font-bold text-gray-900">{{ $passedAttempts }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-md rounded-lg border-l-4 border-purple-600">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-lg mr-4">
                                    <i class="fas fa-star text-purple-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-600 mb-1">{{ __('Average Score') }}</div>
                                    <div class="text-2xl font-bold text-gray-900">
                                        {{ $avgScore ? number_format($avgScore, 1) . '%' : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty State -->
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-3xl text-gray-400"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ __('No exam history yet.') }}</h3>
                        <p class="text-gray-500 text-sm mb-6">Mulai kerjakan ujian untuk melihat riwayat di sini.</p>
                        <a href="{{ route('siswa.exams.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-list"></i>
                            {{ __('Browse Exams') }}
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
