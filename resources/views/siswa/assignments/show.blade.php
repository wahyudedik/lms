<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-tasks mr-2"></i>{{ $assignment->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.my-courses') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Countdown Timer -->
            @if ($remainingTime !== null)
                <div class="bg-white overflow-hidden shadow-md rounded-lg" x-data="{
                    remaining: {{ $remainingTime }},
                    days: 0,
                    hours: 0,
                    minutes: 0,
                    seconds: 0,
                    expired: false,
                    init() {
                        this.updateDisplay();
                        setInterval(() => {
                            if (this.remaining > 0) {
                                this.remaining--;
                                this.updateDisplay();
                            } else {
                                this.expired = true;
                            }
                        }, 1000);
                    },
                    updateDisplay() {
                        this.days = Math.floor(this.remaining / 86400);
                        this.hours = Math.floor((this.remaining % 86400) / 3600);
                        this.minutes = Math.floor((this.remaining % 3600) / 60);
                        this.seconds = this.remaining % 60;
                    }
                }">
                    <div class="p-6">
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">
                            <i class="fas fa-hourglass-half text-blue-600 mr-2"></i>Sisa Waktu Pengumpulan
                        </h3>
                        <div x-show="!expired" class="flex items-center gap-4">
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-blue-600" x-text="days"></span>
                                <span class="text-xs text-gray-500">Hari</span>
                            </div>
                            <span class="text-xl text-gray-400">:</span>
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-blue-600" x-text="hours"></span>
                                <span class="text-xs text-gray-500">Jam</span>
                            </div>
                            <span class="text-xl text-gray-400">:</span>
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-blue-600" x-text="minutes"></span>
                                <span class="text-xs text-gray-500">Menit</span>
                            </div>
                            <span class="text-xl text-gray-400">:</span>
                            <div class="text-center">
                                <span class="block text-2xl font-bold text-blue-600" x-text="seconds"></span>
                                <span class="text-xs text-gray-500">Detik</span>
                            </div>
                        </div>
                        <div x-show="expired" class="text-red-600 font-semibold">
                            <i class="fas fa-times-circle mr-1"></i>Waktu pengumpulan telah berakhir
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                            <i class="fas fa-times-circle mr-1"></i>Deadline telah terlewat
                        </span>
                    </div>
                </div>
            @endif

            <!-- Assignment Details -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Detail Tugas
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">Kursus</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $assignment->course->title }}</dd>
                        </div>

                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">Nilai Maksimal</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $assignment->max_score }}</dd>
                        </div>

                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <dt class="text-xs font-semibold text-orange-700 mb-1">Deadline</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                <i class="fas fa-calendar mr-1"></i>{{ $assignment->deadline->format('d M Y, H:i') }}
                            </dd>
                        </div>

                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <dt class="text-xs font-semibold text-indigo-700 mb-1">Tipe File Diizinkan</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                {{ implode(', ', array_map('strtoupper', $allowedExtensions)) }}
                            </dd>
                        </div>
                    </div>

                    <!-- Late Policy Info -->
                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 mb-4">
                        <dt class="text-xs font-semibold text-gray-700 mb-1">Kebijakan Keterlambatan</dt>
                        <dd class="text-sm text-gray-900">
                            @if ($assignment->late_policy === 'reject')
                                <i class="fas fa-ban text-red-500 mr-1"></i>Pengumpulan setelah deadline <strong>tidak
                                    diterima</strong>.
                            @elseif ($assignment->late_policy === 'allow')
                                <i class="fas fa-check text-green-500 mr-1"></i>Pengumpulan terlambat <strong>diterima
                                    tanpa penalti</strong>.
                            @else
                                <i class="fas fa-percentage text-orange-500 mr-1"></i>Pengumpulan terlambat diterima
                                dengan <strong>penalti {{ $assignment->penalty_percentage }}%</strong>.
                            @endif
                        </dd>
                    </div>

                    @if ($assignment->description)
                        <div class="p-3 bg-gray-50 rounded-lg border border-gray-200">
                            <dt class="text-xs font-semibold text-gray-700 mb-1">Deskripsi</dt>
                            <dd class="text-sm text-gray-900 whitespace-pre-line">{{ $assignment->description }}</dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Current Submission Info -->
            @if ($submission)
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-file-upload text-green-600 mr-2"></i>Pengumpulan Anda
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                <dt class="text-xs font-semibold text-blue-700 mb-1">File</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-file mr-1"></i>{{ $submission->file_name }}
                                </dd>
                            </div>

                            <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                <dt class="text-xs font-semibold text-green-700 mb-1">Dikumpulkan Pada</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ $submission->submitted_at->format('d M Y, H:i') }}
                                </dd>
                            </div>

                            <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                                <dt class="text-xs font-semibold text-purple-700 mb-1">Revisi</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    {{ $submission->revision_count }} kali
                                </dd>
                            </div>

                            <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                                <dt class="text-xs font-semibold text-orange-700 mb-1">Status</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    @if ($submission->status === 'submitted')
                                        <span class="text-blue-600">Dikumpulkan</span>
                                    @elseif ($submission->status === 'late')
                                        <span class="text-orange-600">Terlambat</span>
                                    @elseif ($submission->status === 'graded')
                                        <span class="text-green-600">Dinilai</span>
                                    @endif
                                </dd>
                            </div>
                        </div>

                        @if ($submission->status === 'graded')
                            <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                                <h4 class="text-sm font-bold text-green-800 mb-2">
                                    <i class="fas fa-star mr-1"></i>Hasil Penilaian
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <span class="text-xs text-green-700 font-semibold">Nilai</span>
                                        <p class="text-2xl font-bold text-green-800">
                                            {{ $submission->final_score ?? $submission->score }} /
                                            {{ $assignment->max_score }}
                                        </p>
                                        @if ($submission->penalty_applied)
                                            <p class="text-xs text-orange-600 mt-1">
                                                <i class="fas fa-exclamation-triangle mr-1"></i>Penalti
                                                {{ $submission->penalty_applied }}% diterapkan (Nilai asli:
                                                {{ $submission->score }})
                                            </p>
                                        @endif
                                    </div>
                                    @if ($submission->feedback)
                                        <div>
                                            <span class="text-xs text-green-700 font-semibold">Feedback</span>
                                            <p class="text-sm text-gray-900 mt-1">{{ $submission->feedback }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Upload Form -->
            @php
                $canSubmit = true;
                $submitMessage = '';

                if ($submission && $submission->status === 'graded') {
                    $canSubmit = false;
                    $submitMessage = 'Tugas sudah dinilai, tidak dapat direvisi.';
                } elseif ($assignment->isDeadlinePassed() && $assignment->late_policy === 'reject') {
                    $canSubmit = false;
                    $submitMessage = 'Batas waktu pengumpulan telah berakhir.';
                }
            @endphp

            @if ($canSubmit)
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-upload text-purple-600 mr-2"></i>
                            {{ $submission ? 'Revisi Pengumpulan' : 'Kumpulkan Tugas' }}
                        </h3>

                        @if ($assignment->isDeadlinePassed() && $assignment->late_policy === 'penalty')
                            <div class="mb-4 p-3 bg-orange-50 rounded-lg border border-orange-200">
                                <p class="text-sm text-orange-800">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    Deadline telah lewat. Pengumpulan akan dikenakan penalti
                                    {{ $assignment->penalty_percentage }}%.
                                </p>
                            </div>
                        @endif

                        <form
                            action="{{ route(auth()->user()->getRolePrefix() . '.assignments.submit', $assignment) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="mb-4">
                                <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-file text-gray-400 mr-1"></i>Pilih File <span
                                        class="text-red-500">*</span>
                                </label>
                                <input type="file" name="file" id="file" required
                                    accept="{{ implode(',', array_map(fn($ext) => '.' . $ext, $allowedExtensions)) }}"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="text-xs text-gray-500 mt-2">
                                    Tipe file: {{ implode(', ', array_map('strtoupper', $allowedExtensions)) }} |
                                    Ukuran maks: {{ number_format($maxFileSize / 1048576, 0) }} MB
                                </p>
                                @error('file')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex justify-end">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ $submission ? 'Kirim Revisi' : 'Kumpulkan' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center p-4 bg-gray-50 rounded-lg border border-gray-200">
                            <i class="fas fa-lock text-gray-400 text-xl mr-3"></i>
                            <p class="text-sm text-gray-600">{{ $submitMessage }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
