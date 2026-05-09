<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-folder-plus mr-2"></i>Tambah Kategori Bank Soal
            </h2>
            <a href="{{ route('admin.question-bank-categories.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.question-bank-categories.store') }}">
                        @csrf

                        <!-- Name -->
                        <div class="mb-5">
                            <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                placeholder="Contoh: Matematika, Fisika, Bahasa Indonesia">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-5">
                            <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                Deskripsi (Opsional)
                            </label>
                            <textarea id="description" name="description" rows="3"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                placeholder="Deskripsi singkat kategori ini">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Parent Category -->
                        <div class="mb-5">
                            <label for="parent_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Kategori Induk (Opsional)
                            </label>
                            <select id="parent_id" name="parent_id"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">-- Tidak Ada (Kategori Utama) --</option>
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}"
                                        {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                        {{ $parent->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('parent_id')" class="mt-2" />
                        </div>

                        <!-- Color & Order -->
                        <div class="grid grid-cols-2 gap-4 mb-5">
                            <div>
                                <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Warna <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="color" id="color" name="color"
                                        value="{{ old('color', '#3B82F6') }}"
                                        class="h-10 w-16 rounded-lg border border-gray-300 cursor-pointer">
                                    <span class="text-sm text-gray-500">Pilih warna untuk kategori</span>
                                </div>
                                <x-input-error :messages="$errors->get('color')" class="mt-2" />
                            </div>
                            <div>
                                <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                                    Urutan
                                </label>
                                <input type="number" id="order" name="order" value="{{ old('order', 0) }}"
                                    min="0"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <x-input-error :messages="$errors->get('order')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Active -->
                        <div class="mb-6">
                            <label class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                <span class="text-sm font-semibold text-gray-700">Aktif</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                            <a href="{{ route('admin.question-bank-categories.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i> Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
