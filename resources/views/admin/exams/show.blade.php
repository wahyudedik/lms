<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt mr-2"></i>{{ __('Exam Details: :title', ['title' => $exam->title]) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.edit', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    {{ __('Edit') }}
                </a>
                <a href="{{ route('admin.exams.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
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
                    <div class="flex flex-wrap justify-between items-center gap-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Exam Status') }}
                            </h3>
                            <div class="mt-2">
                                {!! $exam->status_badge !!}
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <form action="{{ route('admin.exams.toggle-status', $exam) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-{{ $exam->is_published ? 'red' : 'green' }}-600 text-white font-semibold rounded-lg hover:bg-{{ $exam->is_published ? 'red' : 'green' }}-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-{{ $exam->is_published ? 'eye-slash' : 'eye' }}"></i>
                                    {{ $exam->is_published ? __('Sembunyikan') : __('Publikasikan') }}
                                </button>
                            </form>

                            <form action="{{ route('admin.exams.duplicate', $exam) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-copy"></i>{{ __('Duplikat') }}
                                </button>
                            </form>

                            <a href="{{ route('admin.exams.results', $exam) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-chart-bar"></i>{{ __('Lihat Hasil') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Informasi Dasar') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-600">{{ __('Kursus') }}</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $exam->course->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600">{{ __('Dibuat Oleh') }}</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $exam->creator->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600">{{ __('Durasi') }}</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">
                                <i class="fas fa-clock text-gray-400 mr-1"></i>{{ $exam->duration_minutes }} {{ trans_choice(__(':count menit'), $exam->duration_minutes, ['count' => $exam->duration_minutes]) }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600">{{ __('Maksimal Percobaan') }}</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $exam->max_attempts }}x</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600">{{ __('Nilai Lulus') }}</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ $exam->pass_score }}%</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-600">{{ __('Total Soal') }}</label>
                            <p class="mt-1 text-sm font-semibold text-gray-900">{{ trans_choice(__(':count soal'), $exam->total_questions, ['count' => $exam->total_questions]) }}</p>
                        </div>
                    </div>

                    @if ($exam->description)
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-blue-600 mr-1"></i>{{ __('Deskripsi') }}
                            </label>
                            <p class="text-sm text-gray-900">{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if ($exam->instructions)
                        <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-list-ol text-green-600 mr-1"></i>{{ __('Instruksi') }}
                            </label>
                            <p class="text-sm text-gray-900 whitespace-pre-line">{{ $exam->instructions }}</p>
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

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="p-4 bg-green-50 rounded-lg border border-green-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-plus text-green-600 mr-1"></i>{{ __('Waktu Mulai') }}
                            </label>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $exam->start_time ? $exam->start_time->translatedFormat('d M Y, H:i') : __('Tidak dibatasi') }}
                            </p>
                        </div>

                        <div class="p-4 bg-red-50 rounded-lg border border-red-100">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-calendar-check text-red-600 mr-1"></i>{{ __('Waktu Selesai') }}
                            </label>
                            <p class="text-sm font-semibold text-gray-900">
                                {{ $exam->end_time ? $exam->end_time->translatedFormat('d M Y, H:i') : __('Tidak dibatasi') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Token Access -->
            @if ($exam->allow_token_access)
                <div class="bg-white overflow-hidden shadow-md rounded-lg border-2 border-indigo-200">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-ticket-alt text-indigo-600 mr-2"></i>{{ __('Akses via Token (Guest Access)') }}
                        </h3>

                        <div class="bg-indigo-50 rounded-lg p-6 mb-4 border border-indigo-200">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex-1">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">{{ __('Token Akses') }}</label>
                                    <div class="flex items-center gap-3">
                                        <code id="access-token"
                                            class="text-3xl font-bold tracking-widest text-indigo-600 bg-white px-6 py-3 rounded-lg border-2 border-indigo-300 shadow-sm">
                                            {{ $exam->access_token }}
                                        </code>
                                        <button onclick="copyToken()"
                                            class="inline-flex items-center gap-2 px-4 py-3 bg-indigo-600 text-white font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                            <i class="fas fa-copy"></i>{{ __('Salin') }}
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2">
                                        <i class="fas fa-info-circle"></i> {{ __('Bagikan token ini kepada peserta ujian') }}
                                    </p>
                                </div>
                            </div>

                            <div class="border-t border-indigo-200 pt-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="p-3 bg-blue-50 rounded-lg border border-blue-200">
                                        <label class="block text-xs font-semibold text-blue-700 mb-1">{{ __('URL Akses') }}</label>
                                        <div class="flex items-center gap-2">
                                            <input type="text" id="access-url" readonly
                                                value="{{ route('guest.exams.index') }}"
                                                class="text-xs text-blue-900 bg-transparent border-none p-0 w-full focus:ring-0">
                                            <button onclick="copyUrl()" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-green-50 rounded-lg border border-green-200">
                                        <label class="block text-xs font-semibold text-green-700 mb-1">{{ __('Penggunaan') }}</label>
                                        <p class="text-sm font-bold text-green-900">
                                            {{ $exam->current_token_uses }} / {{ $exam->max_token_uses ?? '∞' }} {{ __('kali') }}
                                        </p>
                                    </div>

                                    <div class="p-3 bg-purple-50 rounded-lg border border-purple-200">
                                        <label class="block text-xs font-semibold text-purple-700 mb-1">{{ __('Persyaratan') }}</label>
                                        <div class="text-xs text-purple-900 space-y-1">
                                            @if ($exam->require_guest_name)
                                                <div><i class="fas fa-check text-green-500 mr-1"></i> {{ __('Wajib Nama') }}</div>
                                            @endif
                                            @if ($exam->require_guest_email)
                                                <div><i class="fas fa-check text-green-500 mr-1"></i> {{ __('Wajib Email') }}</div>
                                            @endif
                                            @if (!$exam->require_guest_name && !$exam->require_guest_email)
                                                <div class="text-gray-500">{{ __('None') }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-sm text-yellow-800">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                <strong>{{ __('Penting:') }}</strong> {{ __('Siapa saja yang memiliki token ini dapat mengakses ujian tanpa perlu login. Pastikan hanya memberikan token kepada peserta yang berhak!') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Settings -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-cog text-purple-600 mr-2"></i>{{ __('Pengaturan') }}
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-{{ $exam->shuffle_questions ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ __('Acak Urutan Soal') }}</span>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-{{ $exam->shuffle_options ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ __('Acak Opsi Jawaban') }}</span>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-{{ $exam->show_results_immediately ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ __('Tampilkan Hasil Langsung') }}</span>
                        </div>

                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-{{ $exam->show_correct_answers ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ __('Tampilkan Jawaban Benar') }}</span>
                        </div>

                        <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-{{ $exam->require_fullscreen ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ __('Wajib Fullscreen') }}</span>
                        </div>

                        <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                            <i class="fas fa-{{ $exam->detect_tab_switch ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm font-semibold text-gray-900">{{ __('Deteksi Perpindahan Tab') }}</span>
                            @if ($exam->detect_tab_switch)
                                <span class="ml-2 text-xs text-gray-500">({{ __('Max: :countx', ['count' => $exam->max_tab_switches]) }})</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-question-circle text-orange-600 mr-2"></i>{{ trans_choice(':count Question|:count Questions', $exam->questions->count(), ['count' => $exam->questions->count()]) }}
                        </h3>
                        <a href="{{ route('admin.exams.questions.index', $exam) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-list"></i>{{ __('Manage Questions') }}
                        </a>
                    </div>

                    @if ($exam->questions->count() > 0)
                        <div class="space-y-3">
                            @foreach ($exam->questions as $question)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <div class="flex items-center flex-wrap gap-2 mb-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                    #{{ $question->order }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    <i class="{{ $question->type_icon }} mr-1"></i>{{ $question->type_display }}
                                                </span>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    {{ $question->points }} {{ __('poin') }}
                                                </span>
                                            </div>
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ Str::limit($question->question_text, 150) }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 p-3 bg-green-50 rounded-lg border border-green-200">
                            <p class="text-sm font-semibold text-gray-700">
                                <i class="fas fa-calculator text-green-600 mr-2"></i><strong>{{ __('Total Poin:') }}</strong> {{ $exam->total_points }}
                            </p>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <i class="fas fa-clipboard-question text-6xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-semibold mb-4">{{ __('No questions added yet.') }}</p>
                            <a href="{{ route('admin.exams.questions.index', $exam) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>{{ __('Add Question') }}
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Attempts Summary -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-users text-purple-600 mr-2"></i>{{ trans_choice(':count Exam Attempt|:count Exam Attempts', $exam->attempts->count(), ['count' => $exam->attempts->count()]) }}
                        </h3>
                        @if ($exam->attempts->count() > 0)
                            <a href="{{ route('admin.exams.results', $exam) }}"
                                class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                {{ __('Lihat Semua') }}
                            </a>
                        @endif
                    </div>

                    @if ($exam->attempts->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Siswa') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Waktu') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Nilai') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exam->attempts->take(5) as $attempt)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    @if ($attempt->is_guest)
                                                        {{ $attempt->guest_name ?? __('Guest') }}
                                                    @else
                                                        {{ $attempt->user->name ?? __('User tidak diketahui') }}
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->translatedFormat('d M Y, H:i') : '-' }}
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
                        <div class="flex flex-col items-center justify-center text-gray-500 py-8">
                            <i class="fas fa-users text-4xl text-gray-300 mb-3"></i>
                            <p class="text-sm font-semibold">{{ __('No students have taken this exam yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
    @push('scripts')
        <script>
            function copyToken() {
                const token = document.getElementById('access-token').textContent.trim();
                navigator.clipboard.writeText(token).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'Token berhasil disalin!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            }

            function copyUrl() {
                const url = document.getElementById('access-url').value;
                navigator.clipboard.writeText(url).then(() => {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'URL berhasil disalin!',
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            }
        </script>
    @endpush
</x-app-layout>
