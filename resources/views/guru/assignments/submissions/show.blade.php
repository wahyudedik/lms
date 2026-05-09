<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-file-alt mr-2"></i>Detail Pengumpulan
                </h2>
                <p class="text-sm text-gray-600 mt-1">{{ $assignment->title }} - {{ $submission->user->name }}</p>
            </div>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $assignment) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- File Information -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-file text-blue-600 mr-2"></i>Informasi File
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">Nama File</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $submission->file_name }}</dd>
                        </div>

                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">Ukuran File</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $submission->getFormattedFileSize() }}
                            </dd>
                        </div>

                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <dt class="text-xs font-semibold text-purple-700 mb-1">Waktu Pengumpulan</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                {{ $submission->submitted_at->format('d M Y, H:i:s') }}</dd>
                        </div>

                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <dt class="text-xs font-semibold text-orange-700 mb-1">Revisi ke-</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $submission->revision_count }}</dd>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.', [$assignment, $submission]) }}"
                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-download"></i>
                            Unduh File
                        </a>
                    </div>
                </div>
            </div>

            <!-- Penalty Info (if late) -->
            @if ($submission->status === 'late' || $submission->penalty_applied)
                <div class="bg-orange-50 border border-orange-200 overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-orange-800 mb-2">
                            <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>Informasi Keterlambatan
                        </h3>
                        <p class="text-sm text-orange-700">
                            Tugas ini dikumpulkan setelah deadline.
                            @if ($submission->penalty_applied)
                                Penalti {{ $submission->penalty_applied }}% diterapkan pada nilai.
                            @endif
                        </p>
                    </div>
                </div>
            @endif

            <!-- Existing Grade (if already graded) -->
            @if ($submission->isGraded())
                <div class="bg-green-50 border border-green-200 overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-green-800 mb-4">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>Hasil Penilaian
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="p-3 bg-white rounded-lg border border-green-200">
                                <dt class="text-xs font-semibold text-green-700 mb-1">Nilai</dt>
                                <dd class="text-xl font-bold text-gray-900">
                                    {{ $submission->score }}/{{ $assignment->max_score }}</dd>
                            </div>

                            @if ($submission->final_score !== null && $submission->final_score != $submission->score)
                                <div class="p-3 bg-white rounded-lg border border-orange-200">
                                    <dt class="text-xs font-semibold text-orange-700 mb-1">Nilai Akhir (setelah penalti)
                                    </dt>
                                    <dd class="text-xl font-bold text-gray-900">
                                        {{ number_format($submission->final_score, 2) }}</dd>
                                </div>
                            @endif

                            <div class="p-3 bg-white rounded-lg border border-green-200">
                                <dt class="text-xs font-semibold text-green-700 mb-1">Dinilai Pada</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ $submission->graded_at->format('d M Y, H:i') }}</dd>
                            </div>
                        </div>

                        @if ($submission->grader)
                            <p class="text-sm text-green-700 mb-2">
                                <i class="fas fa-user mr-1"></i>Dinilai oleh: {{ $submission->grader->name }}
                            </p>
                        @endif

                        @if ($submission->feedback)
                            <div class="mt-3 p-3 bg-white rounded-lg border border-green-200">
                                <dt class="text-xs font-semibold text-green-700 mb-1">Feedback</dt>
                                <dd class="text-sm text-gray-900 whitespace-pre-line">{{ $submission->feedback }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Grading Form -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-pen text-green-600 mr-2"></i>
                        {{ $submission->isGraded() ? 'Perbarui Nilai' : 'Beri Nilai' }}
                    </h3>

                    <form action="{{ route(auth()->user()->getRolePrefix() . '.', [$assignment, $submission]) }}"
                        method="POST">
                        @csrf

                        <div class="mb-6">
                            <label for="score" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-star text-gray-400 mr-1"></i>Nilai (0 - {{ $assignment->max_score }})
                                <span class="text-red-500">*</span>
                            </label>
                            <input type="number" name="score" id="score"
                                value="{{ old('score', $submission->score) }}" min="0"
                                max="{{ $assignment->max_score }}" required
                                class="block w-full md:w-1/2 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                placeholder="Masukkan nilai...">
                            @error('score')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="mb-6">
                            <label for="feedback" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-comment text-gray-400 mr-1"></i>Feedback (Opsional)
                            </label>
                            <textarea name="feedback" id="feedback" rows="4"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                placeholder="Berikan feedback untuk siswa...">{{ old('feedback', $submission->feedback) }}</textarea>
                            @error('feedback')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.', $assignment) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                Batal
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ $submission->isGraded() ? 'Perbarui Nilai' : 'Simpan Nilai' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
