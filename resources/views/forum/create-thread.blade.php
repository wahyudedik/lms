<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle text-purple-600 mr-2"></i>
                {{ isset($thread) ? 'Edit Thread' : 'Buat Thread Baru' }}
            </h2>
            <a href="{{ route('forum.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form
                        action="{{ isset($thread) ? route('forum.update', [$thread->category->slug, $thread->slug]) : route('forum.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($thread))
                            @method('PUT')
                        @endif

                        <!-- Category -->
                        <div class="mb-6">
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-folder text-gray-400 mr-1"></i>Kategori <span
                                    class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Pilih kategori</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $selectedCategory?->id ?? ($thread->category_id ?? '')) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-heading text-gray-400 mr-1"></i>Judul Thread <span
                                    class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $thread->title ?? '') }}" required maxlength="255"
                                placeholder="Masukkan judul yang deskriptif..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-150">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-align-left text-gray-400 mr-1"></i>Konten <span
                                    class="text-red-500">*</span>
                            </label>
                            <textarea name="content" id="content" rows="10" required placeholder="Tulis konten thread Anda..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-150">{{ old('content', $thread->content ?? '') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Minimal 10 karakter</p>
                            @error('content')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tips -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-bold text-blue-900 mb-2">
                                <i class="fas fa-lightbulb mr-2"></i>Tips untuk Thread yang Baik:
                            </h4>
                            <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                                <li>Pilih judul yang jelas dan deskriptif</li>
                                <li>Berikan konteks dan detail yang cukup</li>
                                <li>Bersikap sopan dan konstruktif</li>
                                <li>Gunakan format yang mudah dibaca</li>
                                <li>Pilih kategori yang sesuai</li>
                            </ul>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ url()->previous() }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                <span>Batal</span>
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-{{ isset($thread) ? 'save' : 'paper-plane' }}"></i>
                                <span>{{ isset($thread) ? 'Perbarui Thread' : 'Buat Thread' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
