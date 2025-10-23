<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Edit Materi - {{ $course->title }}
            </h2>
            <a href="{{ route('admin.courses.materials.index', $course) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.courses.materials.update', [$course, $material]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Judul Materi <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $material->title) }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="mb-6">
                            <label for="description"
                                class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $material->description) }}</textarea>
                        </div>

                        <div class="mb-6">
                            <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                Tipe Materi <span class="text-red-500">*</span>
                            </label>
                            <select name="type" id="type" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
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
                                <div class="mb-4 p-4 bg-gray-50 rounded">
                                    <p class="text-sm text-gray-700">File saat ini:
                                        <strong>{{ $material->file_name }}</strong>
                                        ({{ $material->getFormattedFileSize() }})</p>
                                </div>
                            @endif
                        @endif

                        <div id="file-upload-section"
                            class="mb-6 {{ $material->type === 'file' || $material->type === 'video' ? '' : 'hidden' }}">
                            <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                Upload File Baru (opsional)
                            </label>
                            <input type="file" name="file" id="file" class="w-full">
                            <p class="mt-1 text-sm text-gray-500">Maksimal 50MB. Biarkan kosong jika tidak ingin
                                mengubah file.</p>
                        </div>

                        <div id="url-section"
                            class="mb-6 {{ $material->type === 'youtube' || $material->type === 'link' ? '' : 'hidden' }}">
                            <label for="url" class="block text-sm font-medium text-gray-700 mb-2">URL</label>
                            <input type="url" name="url" id="url"
                                value="{{ old('url', $material->url) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="order"
                                    class="block text-sm font-medium text-gray-700 mb-2">Urutan</label>
                                <input type="number" name="order" id="order"
                                    value="{{ old('order', $material->order) }}" min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>

                            <div class="flex items-center">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_published" value="1"
                                        {{ old('is_published', $material->is_published) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm">
                                    <span class="ml-2 text-sm text-gray-600">Publikasikan Materi</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.courses.materials.index', $course) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">Batal</a>
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
