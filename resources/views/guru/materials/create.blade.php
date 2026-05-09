<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle mr-2"></i>Tambah Materi - {{ $course->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form action="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-gray-400 mr-1"></i>Judul Materi <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                placeholder="Masukkan judul materi..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-gray-400 mr-1"></i>Deskripsi
                            </label>
                            <textarea name="description" id="description" rows="4" placeholder="Masukkan deskripsi materi..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Type -->
                        <div class="mb-6">
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-gray-400 mr-1"></i>Tipe Materi <span
                                    class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Pilih Tipe</option>
                                <option value="file" {{ old('type') == 'file' ? 'selected' : '' }}>File (PDF, PPT,
                                    DOC)</option>
                                <option value="video" {{ old('type') == 'video' ? 'selected' : '' }}>Video File
                                </option>
                                <option value="youtube" {{ old('type') == 'youtube' ? 'selected' : '' }}>YouTube
                                </option>
                                <option value="link" {{ old('type') == 'link' ? 'selected' : '' }}>Link External
                                </option>
                            </select>
                            @error('type')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- File Upload (shown when type is file or video) -->
                        <div id="file-upload-section" class="mb-6 hidden">
                            <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-upload text-gray-400 mr-1"></i>Upload File <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="file" name="file" id="file"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('file') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Maksimal 50MB. Format: PDF, PPT, PPTX, DOC, DOCX, MP4,
                                AVI, etc.</p>
                            @error('file')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- URL (shown when type is youtube or link) -->
                        <div id="url-section" class="mb-6 hidden">
                            <label for="url" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-link text-gray-400 mr-1"></i>URL <span class="text-red-500">*</span>
                            </label>
                            <input type="url" name="url" id="url" value="{{ old('url') }}"
                                placeholder="https://..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150 @error('url') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500" id="url-help">
                                Masukkan URL lengkap
                            </p>
                            @error('url')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Order & Published -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-sort-numeric-down text-gray-400 mr-1"></i>Urutan
                                </label>
                                <input type="number" name="order" id="order" value="{{ old('order', 0) }}"
                                    min="0"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="mt-1 text-sm text-gray-500">Materi akan diurutkan berdasarkan angka ini</p>
                            </div>

                            <div class="flex items-center">
                                <label
                                    class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200 cursor-pointer hover:bg-green-100 transition-colors">
                                    <input type="checkbox" name="is_published" value="1"
                                        {{ old('is_published', true) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Publikasikan Materi</span>
                                </label>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const typeSelect = document.getElementById('type');
            const fileSection = document.getElementById('file-upload-section');
            const urlSection = document.getElementById('url-section');
            const fileInput = document.getElementById('file');
            const urlInput = document.getElementById('url');
            const urlHelp = document.getElementById('url-help');

            typeSelect.addEventListener('change', function() {
                const type = this.value;

                // Hide all sections
                fileSection.classList.add('hidden');
                urlSection.classList.add('hidden');
                fileInput.removeAttribute('required');
                urlInput.removeAttribute('required');

                // Show relevant section
                if (type === 'file' || type === 'video') {
                    fileSection.classList.remove('hidden');
                    fileInput.setAttribute('required', 'required');
                } else if (type === 'youtube' || type === 'link') {
                    urlSection.classList.remove('hidden');
                    urlInput.setAttribute('required', 'required');

                    if (type === 'youtube') {
                        urlHelp.textContent = 'Masukkan URL YouTube (contoh: https://www.youtube.com/watch?v=xxxxx)';
                    } else {
                        urlHelp.textContent = 'Masukkan URL lengkap';
                    }
                }
            });

            // Trigger on load if old value exists
            if (typeSelect.value) {
                typeSelect.dispatchEvent(new Event('change'));
            }
        </script>
    @endpush
</x-app-layout>
