<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-balance-scale mr-2"></i>Bobot Nilai - {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Konfigurasi bobot penilaian tugas dan ujian</p>
            </div>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
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
                    assignmentWeight: {{ old('assignment_weight', $gradeWeight->assignment_weight) }},
                    examWeight: {{ old('exam_weight', $gradeWeight->exam_weight) }},
                    get total() { return parseInt(this.assignmentWeight || 0) + parseInt(this.examWeight || 0); },
                    get isValid() { return this.total === 100; }
                }">
                    <!-- Explanation -->
                    <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                        <h4 class="text-sm font-bold text-blue-800 mb-2">
                            <i class="fas fa-info-circle mr-1"></i>Tentang Bobot Nilai
                        </h4>
                        <p class="text-sm text-blue-700">
                            Bobot nilai menentukan proporsi kontribusi tugas dan ujian terhadap nilai akhir kursus.
                            Total bobot tugas dan ujian harus berjumlah 100%.
                        </p>
                        <p class="text-sm text-blue-700 mt-2">
                            <strong>Rumus:</strong> Nilai Akhir = (Rata-rata Tugas × Bobot Tugas / 100) + (Rata-rata
                            Ujian × Bobot Ujian / 100)
                        </p>
                    </div>

                    <!-- Current Weights Display -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <h4 class="text-sm font-bold text-gray-700 mb-2">
                            <i class="fas fa-chart-pie mr-1"></i>Bobot Saat Ini
                        </h4>
                        <div class="flex gap-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                <i class="fas fa-tasks mr-1"></i>Tugas: {{ $gradeWeight->assignment_weight }}%
                            </span>
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800">
                                <i class="fas fa-file-alt mr-1"></i>Ujian: {{ $gradeWeight->exam_weight }}%
                            </span>
                        </div>
                    </div>

                    <form
                        action="{{ route(auth()->user()->getRolePrefix() . '.courses.grade-weights.update', $course) }}"
                        method="POST">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label for="assignment_weight" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-tasks text-blue-500 mr-1"></i>Bobot Tugas (%) <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="number" name="assignment_weight" id="assignment_weight"
                                    x-model="assignmentWeight" min="0" max="100" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="Contoh: 30">
                                @error('assignment_weight')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="exam_weight" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file-alt text-purple-500 mr-1"></i>Bobot Ujian (%) <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="number" name="exam_weight" id="exam_weight" x-model="examWeight"
                                    min="0" max="100" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="Contoh: 70">
                                @error('exam_weight')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Total Indicator -->
                        <div class="mb-6 p-4 rounded-lg border"
                            :class="isValid ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200'">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-semibold"
                                    :class="isValid ? 'text-green-800' : 'text-red-800'">
                                    <i class="fas mr-1"
                                        :class="isValid ? 'fa-check-circle' : 'fa-exclamation-circle'"></i>
                                    Total: <span x-text="total"></span>%
                                </span>
                                <span class="text-xs" :class="isValid ? 'text-green-600' : 'text-red-600'">
                                    <span x-show="isValid">Total sudah benar (100%)</span>
                                    <span x-show="!isValid">Total harus berjumlah 100%</span>
                                </span>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit" :disabled="!isValid"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md disabled:opacity-50 disabled:cursor-not-allowed"
                                :class="!isValid && 'opacity-50 cursor-not-allowed'">
                                <i class="fas fa-save"></i>
                                Simpan Bobot
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
