<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exam Details: :title', ['title' => $exam->title]) }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.exams.edit', $exam) }}"
                    class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>{{ __('Edit') }}
                </a>
                <a href="{{ route('admin.exams.index') }}"
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
                            <form action="{{ route('admin.exams.toggle-status', $exam) }}" method="POST"
                                class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-{{ $exam->is_published ? 'red' : 'green' }}-500 hover:bg-{{ $exam->is_published ? 'red' : 'green' }}-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-{{ $exam->is_published ? 'eye-slash' : 'eye' }} mr-2"></i>
                                    {{ $exam->is_published ? __('Sembunyikan') : __('Publikasikan') }}
                                </button>
                            </form>

                            <form action="{{ route('admin.exams.duplicate', $exam) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit"
                                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-copy mr-2"></i>{{ __('Duplikat') }}
                                </button>
                            </form>

                            <a href="{{ route('admin.exams.results', $exam) }}"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-chart-bar mr-2"></i>{{ __('Lihat Hasil') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Basic Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Informasi Dasar') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Kursus') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->course->title }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Dibuat Oleh') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->creator->name }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Durasi') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                <i class="fas fa-clock mr-1"></i>{{ $exam->duration_minutes }} {{ trans_choice(__(':count menit'), $exam->duration_minutes, ['count' => $exam->duration_minutes]) }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Maksimal Percobaan') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->max_attempts }}x</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Nilai Lulus') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->pass_score }}%</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Total Soal') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ trans_choice(__(':count soal'), $exam->total_questions, ['count' => $exam->total_questions]) }}</p>
                        </div>
                    </div>

                    @if ($exam->description)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Deskripsi') }}</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $exam->description }}</p>
                        </div>
                    @endif

                    @if ($exam->instructions)
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700">{{ __('Instruksi') }}</label>
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
                            <label class="block text-sm font-medium text-gray-700">{{ __('Waktu Mulai') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $exam->start_time ? $exam->start_time->translatedFormat('d M Y, H:i') : __('Tidak dibatasi') }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">{{ __('Waktu Selesai') }}</label>
                            <p class="mt-1 text-sm text-gray-900">
                                {{ $exam->end_time ? $exam->end_time->translatedFormat('d M Y, H:i') : __('Tidak dibatasi') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Token Access -->
            @if ($exam->allow_token_access)
                <div
                    class="bg-gradient-to-r from-indigo-50 to-purple-50 overflow-hidden shadow-lg sm:rounded-lg border-2 border-indigo-200">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-indigo-900 mb-4">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            {{ __('Akses via Token (Guest Access)') }}
                        </h3>

                        <div class="bg-white rounded-lg p-6 mb-4 shadow">
                            <div class="flex items-center justify-between mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Token Akses') }}</label>
                                    <div class="flex items-center gap-3">
                                        <code id="access-token"
                                            class="text-3xl font-bold tracking-widest text-indigo-600 bg-indigo-50 px-6 py-3 rounded-lg border-2 border-indigo-200">
                                            {{ $exam->access_token }}
                                        </code>
                                        <button onclick="copyToken()"
                                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg transition">
                                            <i class="fas fa-copy mr-2"></i>{{ __('Salin') }}
                                        </button>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle"></i> {{ __('Bagikan token ini kepada peserta ujian') }}
                                    </p>
                                </div>
                            </div>

                            <div class="border-t pt-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div class="p-3 bg-blue-50 rounded-lg">
                                        <label class="block text-xs font-medium text-blue-700">{{ __('URL Akses') }}</label>
                                        <div class="flex items-center gap-2 mt-1">
                                            <input type="text" id="access-url" readonly
                                                value="{{ route('guest.exams.index') }}"
                                                class="text-xs text-blue-900 bg-transparent border-none p-0 w-full">
                                            <button onclick="copyUrl()" class="text-blue-600 hover:text-blue-800">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="p-3 bg-green-50 rounded-lg">
                                        <label class="block text-xs font-medium text-green-700">{{ __('Penggunaan') }}</label>
                                        <p class="text-sm font-bold text-green-900 mt-1">
                                            {{ $exam->current_token_uses }} / {{ $exam->max_token_uses ?? '∞' }} {{ __('kali') }}
                                        </p>
                                    </div>

                                    <div class="p-3 bg-purple-50 rounded-lg">
                                        <label class="block text-xs font-medium text-purple-700">{{ __('Persyaratan') }}</label>
                                        <div class="text-xs text-purple-900 mt-1 space-y-1">
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
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Pengaturan') }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->shuffle_questions ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">{{ __('Acak Urutan Soal') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->shuffle_options ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">{{ __('Acak Opsi Jawaban') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->show_results_immediately ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">{{ __('Tampilkan Hasil Langsung') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->show_correct_answers ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">{{ __('Tampilkan Jawaban Benar') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->require_fullscreen ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">{{ __('Wajib Fullscreen') }}</span>
                        </div>

                        <div class="flex items-center">
                            <i
                                class="fas fa-{{ $exam->detect_tab_switch ? 'check-circle text-green-500' : 'times-circle text-gray-400' }} mr-2"></i>
                            <span class="text-sm text-gray-900">{{ __('Deteksi Perpindahan Tab') }}</span>
                            @if ($exam->detect_tab_switch)
                                <span class="ml-2 text-xs text-gray-500">({{ __('Max: :countx', ['count' => $exam->max_tab_switches]) }})</span>
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
                            {{ trans_choice(':count Question|:count Questions', $exam->questions->count(), ['count' => $exam->questions->count()]) }}
                        </h3>
                        <a href="{{ route('admin.exams.questions.index', $exam) }}"
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-list mr-2"></i>{{ __('Manage Questions') }}
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
                                                    {{ $question->points }} {{ __('poin') }}
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
                                <strong>{{ __('Total Poin:') }}</strong> {{ $exam->total_points }}
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-clipboard-question text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">{{ __('No questions added yet.') }}</p>
                            <a href="{{ route('admin.exams.questions.index', $exam) }}"
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
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ trans_choice(':count Exam Attempt|:count Exam Attempts', $exam->attempts->count(), ['count' => $exam->attempts->count()]) }}
                        </h3>
                        @if ($exam->attempts->count() > 0)
                            <a href="{{ route('admin.exams.results', $exam) }}"
                                class="text-blue-600 hover:text-blue-900">
                                {{ __('Lihat Semua') }}
                            </a>
                        @endif
                    </div>

                    @if ($exam->attempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Siswa') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Waktu') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Nilai') }}</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($exam->attempts->take(5) as $attempt)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">
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
