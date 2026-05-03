<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-file-alt mr-2"></i>{{ __('Create New Exam') }}
            </h2>
            <a href="{{ route('admin.exams.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.exams.store') }}" method="POST">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Informasi Dasar') }}
                            </h3>

                            <div class="mb-6">
                                <label for="course_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-book text-gray-400 mr-1"></i>{{ __('Kursus') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="course_id" id="course_id" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('Pilih Kursus') }}</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course->id }}"
                                            {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-heading text-gray-400 mr-1"></i>{{ __('Exam Title') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>{{ __('Deskripsi') }}
                                </label>
                                <textarea name="description" id="description" rows="3"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('description') }}</textarea>
                            </div>

                            <div class="mb-6">
                                <label for="instructions" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-list-ol text-gray-400 mr-1"></i>{{ __('Exam Instructions') }}
                                </label>
                                <textarea name="instructions" id="instructions" rows="3" placeholder="{{ __('Special instructions for examinees...') }}"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('instructions') }}</textarea>
                            </div>
                        </div>

                        <!-- Time Settings -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-clock text-green-600 mr-2"></i>{{ __('Time Settings') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label for="duration_minutes" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-hourglass-half text-gray-400 mr-1"></i>{{ __('Durasi (menit)') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="duration_minutes" id="duration_minutes"
                                        value="{{ old('duration_minutes', 60) }}" min="1" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>

                                <div>
                                    <label for="start_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-plus text-gray-400 mr-1"></i>{{ __('Waktu Mulai') }}
                                    </label>
                                    <input type="datetime-local" name="start_time" id="start_time"
                                        value="{{ old('start_time') }}"
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>

                                <div>
                                    <label for="end_time" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-check text-gray-400 mr-1"></i>{{ __('Waktu Selesai') }}
                                    </label>
                                    <input type="datetime-local" name="end_time" id="end_time"
                                        value="{{ old('end_time') }}"
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>
                            </div>
                        </div>

                        <!-- Exam Settings -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-cog text-purple-600 mr-2"></i>{{ __('Exam Settings') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="max_attempts" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-redo text-gray-400 mr-1"></i>{{ __('Maksimal Percobaan') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="max_attempts" id="max_attempts"
                                        value="{{ old('max_attempts', 1) }}" min="1" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>

                                <div>
                                    <label for="pass_score" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-percentage text-gray-400 mr-1"></i>{{ __('Pass Score (%)') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="pass_score" id="pass_score"
                                        value="{{ old('pass_score', 60) }}" min="0" max="100"
                                        step="0.01" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="shuffle_questions" value="1"
                                        id="shuffle_questions" {{ old('shuffle_questions') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="shuffle_questions" class="ml-2 text-sm font-semibold text-gray-700">
                                        {{ __('Shuffle Question Order') }}
                                    </label>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="shuffle_options" value="1"
                                        id="shuffle_options" {{ old('shuffle_options') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="shuffle_options" class="ml-2 text-sm font-semibold text-gray-700">
                                        {{ __('Shuffle Answer Options') }}
                                    </label>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="show_results_immediately" value="1"
                                        id="show_results_immediately"
                                        {{ old('show_results_immediately', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="show_results_immediately" class="ml-2 text-sm font-semibold text-gray-700">
                                        {{ __('Tampilkan Hasil Langsung') }}
                                    </label>
                                </div>

                                <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                    <input type="checkbox" name="show_correct_answers" value="1"
                                        id="show_correct_answers"
                                        {{ old('show_correct_answers', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <label for="show_correct_answers" class="ml-2 text-sm font-semibold text-gray-700">
                                        {{ __('Show Correct Answers') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Anti-Cheat Settings -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-shield-alt text-red-600 mr-2"></i>{{ __('Anti-Cheat Settings') }}
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                                    <input type="checkbox" name="require_fullscreen" value="1"
                                        id="require_fullscreen" {{ old('require_fullscreen') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-red-600 shadow-sm">
                                    <label for="require_fullscreen" class="ml-2 text-sm font-semibold text-gray-700">
                                        {{ __('Wajib Fullscreen') }}
                                    </label>
                                </div>

                                <div class="flex items-center p-3 bg-red-50 rounded-lg border border-red-200">
                                    <input type="checkbox" name="detect_tab_switch" value="1"
                                        id="detect_tab_switch" {{ old('detect_tab_switch') ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-red-600 shadow-sm">
                                    <label for="detect_tab_switch" class="ml-2 text-sm font-semibold text-gray-700">
                                        {{ __('Deteksi Perpindahan Tab') }}
                                    </label>
                                </div>
                            </div>

                            <div id="tab_switch_settings" class="{{ old('detect_tab_switch') ? '' : 'hidden' }}">
                                <label for="max_tab_switches" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-exclamation-triangle text-gray-400 mr-1"></i>{{ __('Maksimal Perpindahan Tab') }}
                                </label>
                                <input type="number" name="max_tab_switches" id="max_tab_switches"
                                    value="{{ old('max_tab_switches', 3) }}" min="1"
                                    class="block w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="text-sm text-gray-500 mt-1">{{ __('The exam will be automatically submitted after reaching this limit.') }}</p>
                            </div>
                        </div>

                        <!-- Token Access Settings -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-ticket-alt text-indigo-600 mr-2"></i>{{ __('Akses via Token (Guest Access)') }}
                            </h3>

                            <div class="p-4 bg-indigo-50 rounded-lg mb-4 border border-indigo-200">
                                <p class="text-sm text-indigo-800">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    {{ __('Aktifkan fitur ini jika ingin memberikan akses ujian kepada peserta tanpa harus login. Peserta dapat mengakses ujian via token unik.') }}
                                </p>
                            </div>

                            <div class="flex items-center mb-4 p-3 bg-white rounded-lg border border-gray-200">
                                <input type="checkbox" name="allow_token_access" value="1"
                                    id="allow_token_access" {{ old('allow_token_access') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm"
                                    onchange="toggleTokenSettings()">
                                <label for="allow_token_access" class="ml-2 text-sm font-semibold text-gray-700">
                                    {{ __('Izinkan Akses via Token') }}
                                </label>
                            </div>

                            <div id="token_settings" class="{{ old('allow_token_access') ? '' : 'hidden' }}">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                        <input type="checkbox" name="require_guest_name" value="1"
                                            id="require_guest_name"
                                            {{ old('require_guest_name', true) ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <label for="require_guest_name" class="ml-2 text-sm font-semibold text-gray-700">
                                            {{ __('Wajib Isi Nama') }}
                                        </label>
                                    </div>

                                    <div class="flex items-center p-3 bg-white rounded-lg border border-gray-200">
                                        <input type="checkbox" name="require_guest_email" value="1"
                                            id="require_guest_email" {{ old('require_guest_email') ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                        <label for="require_guest_email" class="ml-2 text-sm font-semibold text-gray-700">
                                            {{ __('Wajib Isi Email') }}
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="max_token_uses" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-users text-gray-400 mr-1"></i>{{ __('Maksimal Penggunaan Token') }}
                                    </label>
                                    <input type="number" name="max_token_uses" id="max_token_uses"
                                        value="{{ old('max_token_uses') }}" min="1"
                                        placeholder="{{ __('Kosongkan untuk unlimited') }}"
                                        class="block w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 focus:ring-opacity-50 transition-all duration-150">
                                    <p class="text-sm text-gray-500 mt-1">
                                        <i class="fas fa-lightbulb mr-1"></i>
                                        {{ __('Kosongkan untuk penggunaan tidak terbatas. Isi angka untuk membatasi jumlah peserta.') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Publish Settings -->
                        <div class="mb-8">
                            <div class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                                <input type="checkbox" name="is_published" value="1" id="is_published"
                                    {{ old('is_published') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-green-600 shadow-sm">
                                <label for="is_published" class="ml-2 text-sm font-semibold text-gray-700">
                                    {{ __('Publish Exam') }}
                                </label>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.exams.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Batal') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ __('Save Exam') }}
                            </button>
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

            function toggleTokenSettings() {
                const checkbox = document.getElementById('allow_token_access');
                const settings = document.getElementById('token_settings');
                settings.classList.toggle('hidden', !checkbox.checked);
            }
        </script>
    @endpush
</x-app-layout>
