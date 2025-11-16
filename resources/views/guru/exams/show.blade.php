<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exam Details: :title', ['title' => $exam->title]) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('guru.exams.edit', $exam) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('guru.exams.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Exam Status & Actions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Exam Status') }}</h3>
                            <div class="mt-2">
                                {!! $exam->status_badge !!}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('guru.exams.questions.index', $exam) }}"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-list mr-2"></i>Kelola Soal
                            </a>

                            <a href="{{ route('guru.exams.review-essays', $exam) }}"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-pen-fancy mr-2"></i>Review Essay
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kursus</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->course->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Dibuat Oleh</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->creator->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Durasi</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-clock mr-1"></i>{{ $exam->duration_minutes }} menit
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Maksimal Percobaan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->max_attempts }}x</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nilai Lulus</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->pass_score }}%</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Total Soal</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->total_questions }} soal</p>
                        </div>
                    </div>

                    @if ($exam->description)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if ($exam->instructions)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">Instruksi</label>
                            <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $exam->instructions }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Exam Schedule') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu Mulai</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $exam->start_time ? $exam->start_time->format('d M Y, H:i') : 'Tidak dibatasi' }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu Selesai</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $exam->end_time ? $exam->end_time->format('d M Y, H:i') : 'Tidak dibatasi' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->shuffle_questions ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">Acak Urutan Soal</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->shuffle_options ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">Acak Opsi Jawaban</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->show_results_immediately ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">Tampilkan Hasil Langsung</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->show_correct_answers ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">Tampilkan Jawaban Benar</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->require_fullscreen ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">Wajib Fullscreen</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->detect_tab_switch ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">Deteksi Perpindahan Tab</span>
                            @if ($exam->detect_tab_switch)
                                <span class="ml-2 text-xs text-gray-500">(Max: {{ $exam->max_tab_switches }}x)</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Soal ({{ $exam->questions->count() }})
                        </h3>
                        <a href="{{ route('guru.exams.questions.index', $exam) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-list mr-2"></i>Kelola Soal
                        </a>
                    </div>

                    @if ($exam->questions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($exam->questions as $question)
                                <div class="border rounded-lg p-4">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span
                                                    class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    #{{ $question->order }}
                                                </span>
                                                <span
                                                    class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    <i
                                                        class="{{ $question->type_icon }} mr-1"></i>{{ $question->type_display }}
                                                </span>
                                                <span
                                                    class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                                    {{ $question->points }} poin
                                                </span>
                                            </div>
                                            <p class="text-sm text-gray-900">
                                                {{ Str::limit($question->question_text, 150) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 text-sm text-gray-600">
                            <strong>Total Poin:</strong> {{ $exam->total_points }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-question text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">{{ __('No questions added yet.') }}</p>
                            <a href="{{ route('guru.exams.questions.index', $exam) }}"
                                class="mt-4 inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-plus mr-2"></i>{{ __('Add Question') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attempts Summary -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        {{ __('Exam Attempts (:count)', ['count' => $exam->attempts->count()]) }}
                    </h3>

                    @if ($exam->attempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Siswa</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Waktu</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Nilai</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exam->attempts->take(5) as $attempt)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $attempt->user?->name ?? ($attempt->guest_name ?? 'Guest') }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $attempt->user?->email ?? ($attempt->guest_email ?? '-') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->score ? number_format($attempt->score, 2) . '%' : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $attempt->status_badge !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-users text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">{{ __('No students have taken this exam yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
