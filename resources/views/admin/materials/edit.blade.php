<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit mr-2"></i>{{ __('Edit Material - :title', ['title' => $course->title]) }}
            </h2>
            <a href="{{ route('admin.courses.materials.index', $course) }}"
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
                    <form action="{{ route('admin.courses.materials.update', [$course, $material]) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Basic Information') }}
                            </h3>

                            <div class="mb-6">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-heading text-gray-400 mr-1"></i>{{ __('Material Title') }} <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title"
                                    value="{{ old('title', $material->title) }}" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>{{ __('Deskripsi') }}
                                </label>
                                <textarea name="description" id="description" rows="4"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">{{ old('description', $material->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Material Type -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-file text-green-600 mr-2"></i>{{ __('Material Type') }}
                            </h3>

                            <div class="mb-6">
                                <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-list text-gray-400 mr-1"></i>{{ __('Material Type') }} <span
                                        class="text-red-500">*</span>
                                </label>
                                <select name="type" id="type" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="file"
                                        {{ old('type', $material->type) == 'file' ? 'selected' : '' }}>
                                        {{ __('File (PDF, PPT, DOC)') }}</option>
                                    <option value="video"
                                        {{ old('type', $material->type) == 'video' ? 'selected' : '' }}>
                                        {{ __('Video File') }}</option>
                                    <option value="youtube"
                                        {{ old('type', $material->type) == 'youtube' ? 'selected' : '' }}>YouTube
                                    </option>
                                    <option value="link"
                                        {{ old('type', $material->type) == 'link' ? 'selected' : '' }}>
                                        {{ __('Link External') }}</option>
                                </select>
                            </div>

                            @if ($material->type === 'file' || $material->type === 'video')
                                @if ($material->file_path)
                                    <div class="mb-6 p-4 bg-green-50 rounded-lg border border-green-200">
                                        <p class="text-sm font-semibold text-green-800">
                                            <i class="fas fa-file text-green-600 mr-2"></i>{{ __('File saat ini:') }}
                                            <strong>{{ $material->file_name }}</strong>
                                            ({{ $material->getFormattedFileSize() }})
                                        </p>
                                    </div>
                                @endif
                            @endif

                            <div id="file-upload-section"
                                class="mb-6 {{ $material->type === 'file' || $material->type === 'video' ? '' : 'hidden' }}">
                                <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i
                                        class="fas fa-upload text-gray-400 mr-1"></i>{{ __('Upload File Baru (opsional)') }}
                                </label>
                                <input type="file" name="file" id="file"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Maksimal 50MB. Biarkan kosong jika tidak ingin mengubah file.') }}
                                </p>
                            </div>

                            <div id="url-section"
                                class="mb-6 {{ $material->type === 'youtube' || $material->type === 'link' ? '' : 'hidden' }}">
                                <label for="url" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-link text-gray-400 mr-1"></i>URL
                                </label>
                                <input type="url" name="url" id="url"
                                    value="{{ old('url', $material->url) }}"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            </div>
                        </div>

                        <!-- Settings -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-cog text-purple-600 mr-2"></i>{{ __('Settings') }}
                            </h3>

                            <!-- Kelompok Target -->
                            @if ($course->courseGroups->count() > 0)
                                <div class="mb-6">
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-users text-teal-600 mr-1"></i>Kelompok Target
                                    </label>
                                    <p class="text-sm text-gray-500 mb-3">Kosongkan untuk semua siswa</p>
                                    <div
                                        class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-4 bg-gray-50 rounded-lg border border-gray-200 max-h-60 overflow-y-auto">
                                        @foreach ($course->courseGroups as $group)
                                            <label
                                                class="flex items-center p-2 rounded-md hover:bg-gray-100 cursor-pointer transition-colors">
                                                <input type="checkbox" name="group_ids[]" value="{{ $group->id }}"
                                                    {{ in_array($group->id, old('group_ids', $material->courseGroups->pluck('id')->toArray())) ? 'checked' : '' }}
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                <span class="ml-2 text-sm text-gray-700">{{ $group->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @error('group_ids')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-sort-numeric-down text-gray-400 mr-1"></i>{{ __('Urutan') }}
                                    </label>
                                    <input type="number" name="order" id="order"
                                        value="{{ old('order', $material->order) }}" min="0"
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                </div>

                                <div class="flex items-center">
                                    <div class="p-3 bg-green-50 rounded-lg border border-green-200 w-full">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" name="is_published" value="1"
                                                {{ old('is_published', $material->is_published) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-500">
                                            <span
                                                class="ml-2 text-sm font-semibold text-gray-700">{{ __('Publish Material') }}</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.courses.materials.index', $course) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Batal') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ __('Simpan Perubahan') }}
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
