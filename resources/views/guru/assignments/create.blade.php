<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle mr-2"></i>Buat Tugas Baru - {{ $course->title }}
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
                <div class="p-6" x-data="{
                    latePolicy: '{{ old('late_policy', 'reject') }}',
                    isPublished: {{ old('is_published') ? 'true' : 'false' }}
                }">
                    <form action="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}" method="POST">
                        @csrf

                        <!-- Informasi Dasar -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informasi Dasar
                            </h3>

                            <div class="mb-6">
                                <label for="title" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-heading text-gray-400 mr-1"></i>Judul Tugas <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="Masukkan judul tugas...">
                                @error('title')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>Deskripsi
                                </label>
                                <textarea name="description" id="description" rows="4"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="Masukkan deskripsi tugas...">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="mb-6">
                                <label for="material_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-link text-gray-400 mr-1"></i>Tautkan ke Materi (Opsional)
                                </label>
                                <select name="material_id" id="material_id"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">-- Tidak ditautkan --</option>
                                    @foreach ($materials as $material)
                                        <option value="{{ $material->id }}"
                                            {{ old('material_id') == $material->id ? 'selected' : '' }}>
                                            {{ $material->title }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('material_id')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Pengaturan Tugas -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-cog text-green-600 mr-2"></i>Pengaturan Tugas
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <div>
                                    <label for="deadline" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-calendar-alt text-gray-400 mr-1"></i>Deadline <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <input type="datetime-local" name="deadline" id="deadline"
                                        value="{{ old('deadline') }}" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    @error('deadline')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="max_score" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-star text-gray-400 mr-1"></i>Nilai Maksimal <span
                                            class="text-red-500">*</span>
                                    </label>
                                    <input type="number" name="max_score" id="max_score"
                                        value="{{ old('max_score', 100) }}" min="1" required
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    @error('max_score')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Tipe File yang Diizinkan -->
                            <div class="mb-6">
                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file text-gray-400 mr-1"></i>Tipe File yang Diizinkan
                                </label>
                                <p class="text-xs text-gray-500 mb-3">Pilih tipe file yang boleh dikumpulkan siswa. Jika
                                    tidak ada yang dipilih, semua tipe diizinkan.</p>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                                    @php
                                        $fileTypes = [
                                            'pdf' => 'PDF',
                                            'doc' => 'DOC',
                                            'docx' => 'DOCX',
                                            'ppt' => 'PPT',
                                            'pptx' => 'PPTX',
                                            'mp4' => 'MP4',
                                            'mov' => 'MOV',
                                            'avi' => 'AVI',
                                        ];
                                        $oldFileTypes = old('allowed_file_types', []);
                                    @endphp
                                    @foreach ($fileTypes as $value => $label)
                                        <label
                                            class="flex items-center p-3 bg-gray-50 rounded-lg border border-gray-200 cursor-pointer hover:bg-blue-50 hover:border-blue-200 transition-colors">
                                            <input type="checkbox" name="allowed_file_types[]"
                                                value="{{ $value }}"
                                                {{ in_array($value, $oldFileTypes) ? 'checked' : '' }}
                                                class="rounded border-gray-300 text-blue-600 shadow-sm">
                                            <span
                                                class="ml-2 text-sm font-semibold text-gray-700">{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('allowed_file_types')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                                @error('allowed_file_types.*')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Kebijakan Keterlambatan -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-clock text-orange-600 mr-2"></i>Kebijakan Keterlambatan
                            </h3>

                            <div class="space-y-3 mb-6">
                                <label
                                    class="flex items-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors"
                                    :class="latePolicy === 'reject' ? 'bg-red-50 border-red-200' : 'bg-white border-gray-200'">
                                    <input type="radio" name="late_policy" value="reject" x-model="latePolicy"
                                        class="text-red-600 border-gray-300">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Tolak - Tidak menerima
                                        pengumpulan setelah deadline</span>
                                </label>

                                <label
                                    class="flex items-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors"
                                    :class="latePolicy === 'allow' ? 'bg-green-50 border-green-200' :
                                        'bg-white border-gray-200'">
                                    <input type="radio" name="late_policy" value="allow" x-model="latePolicy"
                                        class="text-green-600 border-gray-300">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Izinkan - Terima pengumpulan
                                        terlambat tanpa penalti</span>
                                </label>

                                <label
                                    class="flex items-center p-3 rounded-lg border cursor-pointer hover:bg-gray-50 transition-colors"
                                    :class="latePolicy === 'penalty' ? 'bg-orange-50 border-orange-200' :
                                        'bg-white border-gray-200'">
                                    <input type="radio" name="late_policy" value="penalty" x-model="latePolicy"
                                        class="text-orange-600 border-gray-300">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">Penalti - Terima dengan
                                        pengurangan nilai</span>
                                </label>
                            </div>

                            @error('late_policy')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                            <!-- Penalty Percentage (conditional) -->
                            <div x-show="latePolicy === 'penalty'" x-transition class="mt-4">
                                <label for="penalty_percentage"
                                    class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-percentage text-gray-400 mr-1"></i>Persentase Penalti (%) <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="number" name="penalty_percentage" id="penalty_percentage"
                                    value="{{ old('penalty_percentage') }}" min="1" max="100"
                                    class="block w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="Contoh: 20">
                                <p class="text-xs text-gray-500 mt-1">Nilai akhir = Nilai - (Nilai × Penalti / 100)</p>
                                @error('penalty_percentage')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Publish Settings -->
                        <div class="mb-8">
                            <label
                                class="flex items-center p-4 bg-purple-50 rounded-lg border border-purple-200 cursor-pointer hover:bg-purple-100 transition-colors">
                                <input type="checkbox" name="is_published" value="1" x-model="isPublished"
                                    {{ old('is_published') ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-purple-600 shadow-sm">
                                <span class="ml-2 text-sm font-semibold text-gray-700">
                                    Publikasikan Tugas
                                </span>
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Jika dipublikasikan, siswa yang terdaftar akan
                                menerima notifikasi.</p>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $course) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                Simpan Tugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
