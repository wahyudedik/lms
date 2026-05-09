<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt mr-2"></i>{{ __('Exam Details: :title', ['title' => $exam->title]) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Exam Status & Actions -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Exam Status') }}
                            </h3>
                            <div class="mt-2">
                                {!! $exam->status_badge !!}
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-list"></i>
                                Kelola Soal
                            </a>

                            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-orange-600 text-white font-semibold rounded-lg hover:bg-orange-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-pen-fancy"></i>
                                Review Essay
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informasi Dasar
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">Kursus</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $exam->course->title }}</dd>
                        </div>

                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <dt class="text-xs font-semibold text-purple-700 mb-1">Dibuat Oleh</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $exam->creator->name }}</dd>
                        </div>

                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">Durasi</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                <i class="fas fa-clock mr-1"></i>{{ $exam->duration_minutes }} menit
                            </dd>
                        </div>

                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <dt class="text-xs font-semibold text-orange-700 mb-1">Maksimal Percobaan</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $exam->max_attempts }}x</dd>
                        </div>

                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <dt class="text-xs font-semibold text-indigo-700 mb-1">Nilai Lulus</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $exam->pass_score }}%</dd>
                        </div>

                        <div class="p-3 bg-teal-50 rounded-lg border border-teal-100">
                            <dt class="text-xs font-semibold text-teal-700 mb-1">Total Soal</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $exam->total_questions }} soal</dd>
                        </div>
                    </div>

                    @if ($exam->description)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <dt class="text-xs font-semibold text-gray-700 mb-1">Deskripsi</dt>
                            <dd class="text-sm text-gray-900">{{ $exam->description }}</dd>
                        </div>
                    @endif

                    @if ($exam->instructions)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <dt class="text-xs font-semibold text-gray-700 mb-1">Instruksi</dt>
                            <dd class="text-sm text-gray-900 whitespace-pre-line">{{ $exam->instructions }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Schedule -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-calendar text-green-600 mr-2"></i>{{ __('Exam Schedule') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">Waktu Mulai</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                {{ $exam->start_time ? $exam->start_time->format('d M Y, H:i') : 'Tidak dibatasi' }}
                            </dd>
                        </div>

                        <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                            <dt class="text-xs font-semibold text-red-700 mb-1">Waktu Selesai</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                {{ $exam->end_time ? $exam->end_time->format('d M Y, H:i') : 'Tidak dibatasi' }}
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-cog text-purple-600 mr-2"></i>Pengaturan
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div
                            class="flex items-center p-3 rounded-lg border {{ $exam->shuffle_questions ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <i
                                class="fas fa-{{ $exam->shuffle_questions ? 'check-circle text-green-600' : 'times-circle text-gray-400' }} text-xl mr-3"></i>
                            <span class="text-sm font-semibold text-gray-900">Acak Urutan Soal</span>
                        </div>

                        <div
                            class="flex items-center p-3 rounded-lg border {{ $exam->shuffle_options ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <i
                                class="fas fa-{{ $exam->shuffle_options ? 'check-circle text-green-600' : 'times-circle text-gray-400' }} text-xl mr-3"></i>
                            <span class="text-sm font-semibold text-gray-900">Acak Opsi Jawaban</span>
                        </div>

                        <div
                            class="flex items-center p-3 rounded-lg border {{ $exam->show_results_immediately ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <i
                                class="fas fa-{{ $exam->show_results_immediately ? 'check-circle text-green-600' : 'times-circle text-gray-400' }} text-xl mr-3"></i>
                            <span class="text-sm font-semibold text-gray-900">Tampilkan Hasil Langsung</span>
                        </div>

                        <div
                            class="flex items-center p-3 rounded-lg border {{ $exam->show_correct_answers ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <i
                                class="fas fa-{{ $exam->show_correct_answers ? 'check-circle text-green-600' : 'times-circle text-gray-400' }} text-xl mr-3"></i>
                            <span class="text-sm font-semibold text-gray-900">Tampilkan Jawaban Benar</span>
                        </div>

                        <div
                            class="flex items-center p-3 rounded-lg border {{ $exam->require_fullscreen ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }}">
                            <i
                                class="fas fa-{{ $exam->require_fullscreen ? 'check-circle text-red-600' : 'times-circle text-gray-400' }} text-xl mr-3"></i>
                            <span class="text-sm font-semibold text-gray-900">Wajib Fullscreen</span>
                        </div>

                        <div
                            class="flex items-center p-3 rounded-lg border {{ $exam->detect_tab_switch ? 'bg-red-50 border-red-200' : 'bg-gray-50 border-gray-200' }}">
                            <i
                                class="fas fa-{{ $exam->detect_tab_switch ? 'check-circle text-red-600' : 'times-circle text-gray-400' }} text-xl mr-3"></i>
                            <div class="flex-1">
                                <span class="text-sm font-semibold text-gray-900">Deteksi Perpindahan Tab</span>
                                @if ($exam->detect_tab_switch)
                                    <span class="block text-xs text-gray-600 mt-1">(Max:
                                        {{ $exam->max_tab_switches }}x)</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-list text-purple-600 mr-2"></i>Soal ({{ $exam->questions->count() }})
                        </h3>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-list"></i>
                            Kelola Soal
                        </a>
                    </div>

                    @if ($exam->questions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($exam->questions as $question)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center gap-2 mb-2">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    #{{ $question->order }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    <i
                                                        class="{{ $question->type_icon }} mr-1"></i>{{ $question->type_display }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    {{ $question->points }} poin
                                                </span>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ Str::limit($question->question_text, 150) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <span class="text-sm font-semibold text-blue-900">Total Poin:
                                {{ $exam->total_points }}</span>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-clipboard-question text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold mb-2">{{ __('No questions added yet.') }}</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                {{ __('Add Question') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attempts Summary -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i
                            class="fas fa-users text-orange-600 mr-2"></i>{{ __('Exam Attempts (:count)', ['count' => $exam->attempts->count()]) }}
                    </h3>

                    @if ($exam->attempts->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Siswa</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Waktu</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Nilai</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exam->attempts->take(5) as $attempt)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
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
                                                <div class="text-sm font-semibold text-gray-900">
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
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-users text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-sm font-semibold">{{ __('No students have taken this exam yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
