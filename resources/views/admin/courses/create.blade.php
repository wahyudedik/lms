<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Course') }}
            </h2>
            <a href="{{ route('admin.courses.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Course Name') }} <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Course Code') }}
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code') }}"
                                placeholder="{{ __('Kosongkan untuk auto-generate') }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Biarkan kosong untuk generate otomatis (contoh: ABC123)') }}</p>
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Deskripsi') }}
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instructor -->
                        <div class="mb-6">
                            <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Pengajar') }} <span class="text-red-500">*</span>
                            </label>
                            <select name="instructor_id" id="instructor_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('instructor_id') border-red-500 @enderror">
                                <option value="">{{ __('Pilih Pengajar') }}</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}"
                                        {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                        {{ $instructor->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('instructor_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status & Max Students -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Status -->
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Status') }} <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>
                                        {{ __('Draft') }}
                                    </option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>
                                        {{ __('Dipublikasikan') }}</option>
                                    <option value="archived" {{ old('status') == 'archived' ? 'selected' : '' }}>
                                        {{ __('Diarsipkan') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Students -->
                            <div>
                                <label for="max_students" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Maks. Siswa') }}
                                </label>
                                <input type="number" name="max_students" id="max_students"
                                    value="{{ old('max_students') }}" min="1" placeholder="{{ __('Tidak terbatas') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('max_students') border-red-500 @enderror">
                                @error('max_students')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Cover Image -->
                        <div class="mb-6">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Cover Image') }}
                            </label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                class="w-full @error('cover_image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">{{ __('Format: JPG, PNG, GIF. Maksimal 2MB') }}</p>
                            @error('cover_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>{{ __('Simpan') }}
                            </button>
                            <a href="{{ route('admin.courses.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                {{ __('Batal') }}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
