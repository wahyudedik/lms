<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Ujian: {{ $exam->title }}
            </h2>
            <a href="{{ route('admin.exams.show', $exam) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.exams.update', $exam) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Dasar</h3>

                            <div class="mb-4">
                                <label for="course_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kursus <span class="text-red-500">*</span>
                                </label>
                                <select name="course_id" id="course_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Pilih Kursus</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ old('course_id', $exam->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                    Judul Ujian <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title"
                                    value="{{ old('title', $exam->title) }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                                <textarea name="description" id="description" rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $exam->description) }}</textarea>
                            </div>

                            <div class="mb-4">
                                <label for="instructions" class="block text-sm font-medium text-gray-700 mb-2">Instruksi
                                    Ujian</label>
                                <textarea name="instructions" id="instructions" rows="3" placeholder="Instruksi khusus untuk peserta ujian..."
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('instructions', $exam->instructions) }}</textarea>
                            </div>
                        </div>

                        <!-- Time Settings -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Waktu</h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div>
                                    <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                        Durasi (menit) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="duration_minutes" id="duration_minutes"
                                        value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1"
                                        required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="start_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu
                                        Mulai</label>
                                    <input type="datetime-local" name="start_time" id="start_time"
                                        value="{{ old('start_time', $exam->start_time ? $exam->start_time->format('Y-m-d\TH:i') : '') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-medium text-gray-700 mb-2">Waktu
                                        Selesai</label>
                                    <input type="datetime-local" name="end_time" id="end_time"
                                        value="{{ old('end_time', $exam->end_time ? $exam->end_time->format('Y-m-d\TH:i') : '') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Exam Settings -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Ujian</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="max_attempts" class="block text-sm font-medium text-gray-700 mb-2">
                                        Maksimal Percobaan <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="max_attempts" id="max_attempts"
                                        value="{{ old('max_attempts', $exam->max_attempts) }}" min="1" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label for="pass_score" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nilai Lulus (%) <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="pass_score" id="pass_score"
                                        value="{{ old('pass_score', $exam->pass_score) }}" min="0"
                                        max="100" step="0.01" required
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="shuffle_questions" value="1"
                                        id="shuffle_questions"
                                        {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="shuffle_questions" class="ml-2 text-sm text-gray-700">
                                        Acak Urutan Soal
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="shuffle_options" value="1"
                                        id="shuffle_options"
                                        {{ old('shuffle_options', $exam->shuffle_options) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="shuffle_options" class="ml-2 text-sm text-gray-700">
                                        Acak Opsi Jawaban
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="show_results_immediately" value="1"
                                        id="show_results_immediately"
                                        {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="show_results_immediately" class="ml-2 text-sm text-gray-700">
                                        Tampilkan Hasil Langsung
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="show_correct_answers" value="1"
                                        id="show_correct_answers"
                                        {{ old('show_correct_answers', $exam->show_correct_answers) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="show_correct_answers" class="ml-2 text-sm text-gray-700">
                                        Tampilkan Jawaban Benar
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Anti-Cheat Settings -->
                        <div class="mb-8">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengaturan Anti-Cheat</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div class="flex items-center">
                                    <input type="checkbox" name="require_fullscreen" value="1"
                                        id="require_fullscreen"
                                        {{ old('require_fullscreen', $exam->require_fullscreen) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="require_fullscreen" class="ml-2 text-sm text-gray-700">
                                        Wajib Fullscreen
                                    </label>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="detect_tab_switch" value="1"
                                        id="detect_tab_switch"
                                        {{ old('detect_tab_switch', $exam->detect_tab_switch) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="detect_tab_switch" class="ml-2 text-sm text-gray-700">
                                        Deteksi Perpindahan Tab
                                    </label>
                                </div>
                            </div>

                            <div id="tab_switch_settings"
                                class="{{ old('detect_tab_switch', $exam->detect_tab_switch) ? '' : 'hidden' }}">
                                <label for="max_tab_switches" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maksimal Perpindahan Tab
                                </label>
                                <input type="number" name="max_tab_switches" id="max_tab_switches"
                                    value="{{ old('max_tab_switches', $exam->max_tab_switches) }}" min="1"
                                    class="w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="text-sm text-gray-500 mt-1">Ujian akan otomatis dikumpulkan setelah mencapai
                                    batas ini.</p>
                            </div>
                        </div>

                        <!-- Offline Mode Settings -->
                        <div class="mb-8 p-6 bg-indigo-50 rounded-lg border border-indigo-200">
                            <h3 class="text-lg font-semibold mb-4 text-indigo-900">
                                <i class="fas fa-wifi-slash mr-2"></i>Offline Mode (CBT Lab)
                            </h3>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <input type="checkbox" name="offline_enabled" value="1"
                                        id="offline_enabled"
                                        {{ old('offline_enabled', $exam->offline_enabled) ? 'checked' : '' }}
                                        class="mt-1 rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <div class="ml-3">
                                        <label for="offline_enabled" class="text-sm font-medium text-gray-700">
                                            Enable Offline Mode
                                        </label>
                                        <p class="text-sm text-gray-500 mt-1">
                                            Allow students to download and take this exam offline in computer labs
                                        </p>
                                    </div>
                                </div>

                                <div id="offline_settings"
                                    class="{{ old('offline_enabled', $exam->offline_enabled) ? '' : 'hidden' }}">
                                    <label for="offline_cache_duration"
                                        class="block text-sm font-medium text-gray-700 mb-2">
                                        Cache Duration (hours)
                                    </label>
                                    <input type="number" name="offline_cache_duration" id="offline_cache_duration"
                                        value="{{ old('offline_cache_duration', $exam->offline_cache_duration ?? 24) }}"
                                        min="1" max="168"
                                        class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <p class="text-sm text-gray-500 mt-1">
                                        How long the exam will be available offline (1-168 hours, default: 24)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Settings -->
                        <div class="mb-8">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_published" value="1" id="is_published"
                                    {{ old('is_published', $exam->is_published) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm">
                                <label for="is_published" class="ml-2 text-sm text-gray-700">
                                    Publikasikan Ujian
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.exams.show', $exam) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('detect_tab_switch').addEventListener('change', function() {
                const settings = document.getElementById('tab_switch_settings');
                settings.classList.toggle('hidden', !this.checked);
            });

            document.getElementById('offline_enabled').addEventListener('change', function() {
                const settings = document.getElementById('offline_settings');
                settings.classList.toggle('hidden', !this.checked);
            });
        </script>
    @endpush
</x-app-layout>
