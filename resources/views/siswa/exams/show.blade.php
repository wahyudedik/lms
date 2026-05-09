<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt text-blue-600 mr-2"></i>{{ $exam->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                <i class="fas fa-arrow-left"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Exam Status Alert -->
            @php
                $userAttempts = $exam->attempts->where('user_id', auth()->id());
                $attemptCount = $userAttempts->count();
                $latestAttempt = $userAttempts->sortByDesc('created_at')->first();
                $canTakeExam = $attemptCount < $exam->max_attempts;
                $hasInProgressAttempt = $latestAttempt && $latestAttempt->status === 'in_progress';
            @endphp

            @if ($hasInProgressAttempt)
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">{{ __('Exam in Progress') }}</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>Anda memiliki ujian yang belum diselesaikan. Silakan lanjutkan atau ujian akan
                                    otomatis
                                    dikumpulkan saat waktu habis.</p>
                            </div>
                            <div class="mt-4">
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.take', $latestAttempt) }}"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold rounded-lg shadow-sm transition">
                                    <i class="fas fa-play"></i>{{ __('Resume Exam') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif (!$canTakeExam)
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-times-circle text-red-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Batas Percobaan Tercapai</h3>
                            <div class="mt-2 text-sm text-red-700">
                                <p>Anda telah menggunakan semua {{ $exam->max_attempts }} percobaan yang tersedia.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Exam Information -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Exam Information') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-book text-gray-400 mr-1"></i>Kursus
                            </label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->course->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>Durasi
                            </label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->duration_minutes }} menit</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-list-ol text-gray-400 mr-1"></i>{{ __('Number of Questions') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->total_questions }} soal</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-check-circle text-gray-400 mr-1"></i>{{ __('Pass Score') }}
                            </label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->pass_score }}%</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-redo text-gray-400 mr-1"></i>Maksimal Percobaan
                            </label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->max_attempts }}x</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-user-check text-gray-400 mr-1"></i>Percobaan Anda
                            </label>
                            <p class="mt-1 text-sm text-gray-900">{{ $attemptCount }}/{{ $exam->max_attempts }}</p>
                        </div>

                        @if ($exam->start_time)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700">
                                    <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Waktu Mulai
                                </label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $exam->start_time->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endif

                        @if ($exam->end_time)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700">
                                    <i class="fas fa-calendar-times text-gray-400 mr-1"></i>Deadline
                                </label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $exam->end_time->format('d M Y, H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if ($exam->description)
                        <div class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-gray-400 mr-1"></i>Deskripsi
                            </label>
                            <p class="text-sm text-gray-900">{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if ($exam->instructions)
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-clipboard-list text-gray-400 mr-1"></i>{{ __('Instructions') }}
                            </label>
                            <div
                                class="text-sm text-gray-900 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg whitespace-pre-line">
                                {{ $exam->instructions }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Exam Rules -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-gavel text-purple-600 mr-2"></i>{{ __('Exam Rules') }}
                    </h3>

                    <div class="space-y-2 text-sm text-gray-700">
                        <div class="flex items-start">
                            <i
                                class="fas fa-{{ $exam->shuffle_questions ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mt-0.5 mr-2"></i>
                            <span>Urutan soal akan diacak</span>
                        </div>

                        <div class="flex items-start">
                            <i
                                class="fas fa-{{ $exam->shuffle_options ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mt-0.5 mr-2"></i>
                            <span>Opsi jawaban akan diacak</span>
                        </div>

                        <div class="flex items-start">
                            <i
                                class="fas fa-{{ $exam->show_results_immediately ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mt-0.5 mr-2"></i>
                            <span>Hasil akan ditampilkan segera setelah selesai</span>
                        </div>

                        <div class="flex items-start">
                            <i
                                class="fas fa-{{ $exam->show_correct_answers ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mt-0.5 mr-2"></i>
                            <span>Jawaban benar akan ditampilkan setelah selesai</span>
                        </div>

                        @if ($exam->require_fullscreen)
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2"></i>
                                <span class="font-medium">Mode layar penuh (fullscreen) wajib</span>
                            </div>
                        @endif

                        @if ($exam->detect_tab_switch)
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5 mr-2"></i>
                                <span class="font-medium">Perpindahan tab akan dideteksi (Max:
                                    {{ $exam->max_tab_switches }}x)</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Previous Attempts -->
            @if ($attemptCount > 0)
                <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-history text-indigo-600 mr-2"></i>Riwayat Percobaan
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full border border-gray-200 rounded-lg">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            Percobaan</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            Waktu
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            {{ __('Score') }}
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase border-b">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($userAttempts->sortByDesc('created_at') as $index => $attempt)
                                        <tr class="hover:bg-gray-50 transition">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                #{{ $attemptCount - $index }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($attempt->score !== null)
                                                    <span
                                                        class="text-sm font-bold {{ $attempt->score >= $exam->pass_score ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ number_format($attempt->score, 2) }}%
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $attempt->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if ($attempt->status === 'completed')
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.review-attempt', $attempt) }}"
                                                        class="text-blue-600 hover:text-blue-900 font-medium">
                                                        <i class="fas fa-eye mr-1"></i>Review
                                                    </a>
                                                @elseif ($attempt->status === 'in_progress')
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.take', $attempt) }}"
                                                        class="text-yellow-600 hover:text-yellow-900 font-medium">
                                                        <i class="fas fa-play mr-1"></i>Lanjutkan
                                                    </a>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Start Exam Button -->
            @if ($canTakeExam && !$hasInProgressAttempt)
                <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                    <div class="p-6">
                        <form action="{{ route(auth()->user()->getRolePrefix() . '.exams.start', $exam) }}" method="POST">
                            @csrf
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg mb-4">
                                <h4 class="font-semibold text-gray-900 mb-2">
                                    <i class="fas fa-info-circle text-blue-600 mr-2"></i>Siap untuk memulai ujian?
                                </h4>
                                <p class="text-sm text-gray-700">
                                    Pastikan Anda telah membaca semua instruksi dengan seksama. Timer akan dimulai
                                    segera
                                    {{ __('after you click the "Start Exam" button.') }}
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <button type="submit"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg shadow-sm transition text-lg">
                                    <i class="fas fa-pencil-alt"></i>{{ __('Start Exam') }}
                                </button>
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.exams.index') }}"
                                    class="flex-1 inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white font-bold rounded-lg shadow-sm transition text-lg">
                                    <i class="fas fa-times"></i>Batal
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
