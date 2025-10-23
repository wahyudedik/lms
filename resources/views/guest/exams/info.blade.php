<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $exam->title }} - {{ config('app.name', 'Laravel LMS') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-sans antialiased bg-gray-50">
    <div class="min-h-screen py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <!-- Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-t-2xl p-8 shadow-xl">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold">{{ $exam->title }}</h1>
                        <p class="mt-2 text-indigo-100">{{ $exam->course->name }}</p>
                    </div>
                    <div class="text-right">
                        <i class="fas fa-clipboard-check text-5xl opacity-25"></i>
                    </div>
                </div>
            </div>

            <!-- Exam Info -->
            <div class="bg-white shadow-xl rounded-b-2xl overflow-hidden">
                <div class="p-8">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-indigo-600 mr-2"></i>
                        Informasi Ujian
                    </h2>

                    @if ($exam->description)
                        <div class="mb-6 p-4 bg-indigo-50 rounded-lg">
                            <p class="text-gray-700">{{ $exam->description }}</p>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <i class="fas fa-question-circle text-3xl text-blue-600 mb-2"></i>
                            <p class="text-2xl font-bold text-blue-900">{{ $exam->questions->count() }}</p>
                            <p class="text-sm text-blue-700">Soal</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-4 text-center">
                            <i class="fas fa-clock text-3xl text-green-600 mb-2"></i>
                            <p class="text-2xl font-bold text-green-900">{{ $exam->duration_minutes }}</p>
                            <p class="text-sm text-green-700">Menit</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <i class="fas fa-trophy text-3xl text-purple-600 mb-2"></i>
                            <p class="text-2xl font-bold text-purple-900">{{ $exam->pass_score }}%</p>
                            <p class="text-sm text-purple-700">Nilai Lulus</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-4 text-center">
                            <i class="fas fa-redo text-3xl text-orange-600 mb-2"></i>
                            <p class="text-2xl font-bold text-orange-900">{{ $exam->max_attempts }}</p>
                            <p class="text-sm text-orange-700">Percobaan</p>
                        </div>
                    </div>

                    <!-- Instructions -->
                    @if ($exam->instructions)
                        <div class="mb-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                            <h3 class="font-bold text-yellow-900 mb-2">
                                <i class="fas fa-exclamation-triangle mr-2"></i>Instruksi Penting:
                            </h3>
                            <div class="text-yellow-800 prose prose-sm">
                                {!! nl2br(e($exam->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Rules -->
                    <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-bold text-gray-800 mb-3">
                            <i class="fas fa-list-check text-indigo-600 mr-2"></i>Aturan Ujian:
                        </h3>
                        <ul class="space-y-2 text-gray-700">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Durasi ujian: <strong>{{ $exam->duration_minutes }} menit</strong></span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                <span>Jumlah soal: <strong>{{ $exam->questions->count() }} soal</strong></span>
                            </li>
                            @if ($exam->shuffle_questions)
                                <li class="flex items-start">
                                    <i class="fas fa-shuffle text-blue-500 mr-2 mt-1"></i>
                                    <span>Urutan soal akan <strong>diacak</strong></span>
                                </li>
                            @endif
                            @if ($exam->require_fullscreen)
                                <li class="flex items-start">
                                    <i class="fas fa-expand text-purple-500 mr-2 mt-1"></i>
                                    <span><strong>Wajib fullscreen</strong> selama ujian</span>
                                </li>
                            @endif
                            @if ($exam->detect_tab_switch)
                                <li class="flex items-start">
                                    <i class="fas fa-eye text-red-500 mr-2 mt-1"></i>
                                    <span>Perpindahan tab akan <strong>dideteksi</strong> (maks
                                        {{ $exam->max_tab_switches }}x)</span>
                                </li>
                            @endif
                            @if ($exam->show_results_immediately)
                                <li class="flex items-start">
                                    <i class="fas fa-chart-line text-indigo-500 mr-2 mt-1"></i>
                                    <span>Hasil akan <strong>ditampilkan langsung</strong> setelah selesai</span>
                                </li>
                            @endif
                        </ul>
                    </div>

                    <!-- Guest Information Form -->
                    <form method="POST" action="{{ route('guest.exams.start', $exam) }}" id="guestForm">
                        @csrf

                        <h3 class="text-xl font-bold text-gray-800 mb-4">
                            <i class="fas fa-user-circle text-indigo-600 mr-2"></i>
                            Informasi Peserta
                        </h3>

                        @if ($exam->require_guest_name)
                            <div class="mb-4">
                                <label for="guest_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="guest_name" id="guest_name" value="{{ old('guest_name') }}"
                                    required placeholder="Masukkan nama lengkap Anda"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('guest_name') border-red-500 @enderror">
                                @error('guest_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        @if ($exam->require_guest_email)
                            <div class="mb-6">
                                <label for="guest_email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input type="email" name="guest_email" id="guest_email"
                                    value="{{ old('guest_email') }}" required placeholder="Masukkan email Anda"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('guest_email') border-red-500 @enderror">
                                @error('guest_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endif

                        <!-- Agreement -->
                        <div class="mb-6">
                            <label class="flex items-start">
                                <input type="checkbox" required id="agreement"
                                    class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">
                                    Saya telah membaca dan memahami aturan ujian, dan bersedia mengikuti ujian dengan
                                    jujur. <span class="text-red-500">*</span>
                                </span>
                            </label>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <a href="{{ route('guest.exams.index') }}"
                                class="flex-1 px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-lg hover:bg-gray-200 transition-colors text-center">
                                <i class="fas fa-arrow-left mr-2"></i> Batal
                            </a>
                            <button type="submit"
                                class="flex-1 px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg shadow-lg hover:from-indigo-700 hover:to-purple-700 transition-all transform hover:scale-105">
                                <i class="fas fa-play-circle mr-2"></i> Mulai Ujian
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Confirm before starting
        document.getElementById('guestForm').addEventListener('submit', function(e) {
            if (!document.getElementById('agreement').checked) {
                e.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Perhatian!',
                    text: 'Anda harus menyetujui aturan ujian terlebih dahulu.',
                });
                return;
            }

            e.preventDefault();
            Swal.fire({
                title: 'Mulai Ujian?',
                html: `<p>Setelah Anda mulai, ujian tidak dapat dibatalkan!</p>
                       <p class="text-sm text-gray-600 mt-2">Timer akan dimulai dan Anda punya <strong>${{ $exam->duration_minutes }} menit</strong> untuk menyelesaikan ujian.</p>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#4F46E5',
                cancelButtonColor: '#6B7280',
                confirmButtonText: '<i class="fas fa-check mr-2"></i> Ya, Mulai Ujian',
                cancelButtonText: '<i class="fas fa-times mr-2"></i> Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    </script>
</body>

</html>
