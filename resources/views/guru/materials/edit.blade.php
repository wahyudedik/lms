<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit mr-2"></i>Edit Materi - {{ $course->title }}
            </h2>
            <a href="{{ route('guru.courses.materials.index', $course) }}"
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
                    <form action="{{ route('guru.courses.materials.update', [$course, $material]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-gray-400 mr-1"></i>Judul Materi <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $material->title) }}" required
                                placeholder="Masukkan judul materi..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                        </div>

                        <div class="mb-6">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-gray-400 mr-1"></i>Deskripsi
                            </label>
                            <textarea name="description" id="description" rows="4" placeholder="Masukkan deskripsi materi..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('description', $material->description) }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-layer-group text-gray-400 mr-1"></i>Tipe Materi <span
                                    class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="file" {{ old('type', $material->type) == 'file' ? 'selected' : '' }}>
                                    File (PDF, PPT, DOC)</option>
                                <option value="video" {{ old('type', $material->type) == 'video' ? 'selected' : '' }}>
                                    Video File</option>
                                <option value="youtube"
                                    {{ old('type', $material->type) == 'youtube' ? 'selected' : '' }}>YouTube</option>
                                <option value="link" {{ old('type', $material->type) == 'link' ? 'selected' : '' }}>
                                    Link External</option>
                            </select>
                        </div>

                        @if ($material->type === 'file' || $material->type === 'video')
                            @if ($material->file_path)
                                <div class="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-100">
                                    <p class="text-sm text-gray-700">
                                        <i class="fas fa-file text-blue-600 mr-2"></i>File saat ini:
                                        <strong>{{ $material->file_name }}</strong>
                                        ({{ $material->getFormattedFileSize() }})
                                    </p>
                                </div>
                            @endif
                        @endif

                        <div id="file-upload-section"
                            class="mb-6 {{ $material->type === 'file' || $material->type === 'video' ? '' : 'hidden' }}">
                            <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-upload text-gray-400 mr-1"></i>Upload File Baru (opsional)
                            </label>
                            <input type="file" name="file" id="file"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            <p class="mt-1 text-sm text-gray-500">Maksimal 50MB. Biarkan kosong jika tidak ingin
                                mengubah file.</p>
                        </div>

                        <div id="url-section"
                            class="mb-6 {{ $material->type === 'youtube' || $material->type === 'link' ? '' : 'hidden' }}">
                            <label for="url" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-link text-gray-400 mr-1"></i>URL
                            </label>
                            <input type="url" name="url" id="url"
                                value="{{ old('url', $material->url) }}" placeholder="https://..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-sort-numeric-down text-gray-400 mr-1"></i>Urutan
                                </label>
                                <input type="number" name="order" id="order"
                                    value="{{ old('order', $material->order) }}" min="0"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            </div>

                            <div class="flex items-center">
                                <label
                                    class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200 cursor-pointer hover:bg-green-100 transition-colors">
                                    <input type="checkbox" name="is_published" value="1"
                                        {{ old('is_published', $material->is_published) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Publikasikan Materi</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('guru.courses.materials.index', $course) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('type').addEventListener('change', function() {
                const type = this.value;
                document.getElementById('file-upload-section').classList.toggle('hidden', type !== 'file' && type !==
                    'video');
                document.getElementById('url-section').classList.toggle('hidden', type !== 'youtube' && type !==
                    'link');
            });
        </script>
    @endpush
</x-app-layout>
