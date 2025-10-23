<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Kelas') }}
            </h2>
            <a href="{{ route('admin.courses.show', $course) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.courses.update', $course) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Nama Kelas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $course->title) }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div class="mb-6">
                            <label for="code" class="block text-sm font-medium text-gray-700 mb-2">
                                Kode Kelas <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code', $course->code) }}"
                                required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('code') border-red-500 @enderror">
                            @error('code')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Deskripsi
                            </label>
                            <textarea name="description" id="description" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('description') border-red-500 @enderror">{{ old('description', $course->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Instructor -->
                        <div class="mb-6">
                            <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Pengajar <span class="text-red-500">*</span>
                            </label>
                            <select name="instructor_id" id="instructor_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('instructor_id') border-red-500 @enderror">
                                <option value="">Pilih Pengajar</option>
                                @foreach ($instructors as $instructor)
                                    <option value="{{ $instructor->id }}"
                                        {{ old('instructor_id', $course->instructor_id) == $instructor->id ? 'selected' : '' }}>
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
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status" id="status" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('status') border-red-500 @enderror">
                                    <option value="draft"
                                        {{ old('status', $course->status) == 'draft' ? 'selected' : '' }}>Draft
                                    </option>
                                    <option value="published"
                                        {{ old('status', $course->status) == 'published' ? 'selected' : '' }}>
                                        Dipublikasikan</option>
                                    <option value="archived"
                                        {{ old('status', $course->status) == 'archived' ? 'selected' : '' }}>Diarsipkan
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Students -->
                            <div>
                                <label for="max_students" class="block text-sm font-medium text-gray-700 mb-2">
                                    Maks. Siswa
                                </label>
                                <input type="number" name="max_students" id="max_students"
                                    value="{{ old('max_students', $course->max_students) }}" min="1"
                                    placeholder="Tidak terbatas"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('max_students') border-red-500 @enderror">
                                @error('max_students')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Cover Image -->
                        @if ($course->cover_image)
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Cover Saat Ini</label>
                                <img src="{{ Storage::url($course->cover_image) }}" alt="Cover"
                                    class="w-48 h-32 object-cover rounded">
                            </div>
                        @endif

                        <!-- Cover Image -->
                        <div class="mb-6">
                            <label for="cover_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Ubah Cover Image
                            </label>
                            <input type="file" name="cover_image" id="cover_image" accept="image/*"
                                class="w-full @error('cover_image') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB. Biarkan kosong
                                jika tidak ingin mengubah.</p>
                            @error('cover_image')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Buttons -->
                        <div class="flex gap-4">
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                <i class="fas fa-save mr-2"></i>Simpan Perubahan
                            </button>
                            <a href="{{ route('admin.courses.show', $course) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
