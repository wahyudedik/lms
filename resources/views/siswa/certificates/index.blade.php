<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-certificate text-yellow-600 mr-2"></i>Sertifikat Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-yellow-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Total Sertifikat</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $certificates->total() }}</p>
                        </div>
                        <div class="bg-yellow-100 rounded-full p-3">
                            <i class="fas fa-certificate text-yellow-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-green-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Kursus Selesai</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['completed_courses'] ?? 0 }}</p>
                        </div>
                        <div class="bg-green-100 rounded-full p-3">
                            <i class="fas fa-check-circle text-green-600 text-xl"></i>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 border-l-4 border-blue-500">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-gray-600 font-semibold uppercase">Rata-rata Nilai</p>
                            <p class="text-3xl font-bold text-gray-900 mt-2">
                                {{ number_format($stats['average_score'] ?? 0, 1) }}%</p>
                        </div>
                        <div class="bg-blue-100 rounded-full p-3">
                            <i class="fas fa-star text-blue-600 text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificates List -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-list text-yellow-600 mr-2"></i>Daftar Sertifikat
                    </h3>
                </div>

                @if ($certificates->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 p-6">
                        @foreach ($certificates as $certificate)
                            <div
                                class="bg-white border-2 border-gray-200 rounded-lg overflow-hidden hover:shadow-lg hover:border-yellow-500 transition">
                                <!-- Certificate Preview -->
                                <div
                                    class="bg-gradient-to-br from-yellow-50 to-orange-50 p-6 text-center border-b-2 border-yellow-500">
                                    <div
                                        class="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-3 shadow-md">
                                        <i class="fas fa-certificate text-yellow-600 text-3xl"></i>
                                    </div>
                                    <h4 class="font-bold text-gray-900 text-lg mb-1">Sertifikat</h4>
                                    <p class="text-sm text-gray-600">{{ $certificate->course->title }}</p>
                                </div>

                                <!-- Certificate Info -->
                                <div class="p-4 space-y-3">
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">
                                            <i class="fas fa-calendar text-yellow-600 mr-1"></i>Tanggal:
                                        </span>
                                        <span
                                            class="font-semibold text-gray-900">{{ $certificate->issued_at->format('d M Y') }}</span>
                                    </div>

                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">
                                            <i class="fas fa-star text-yellow-600 mr-1"></i>Nilai:
                                        </span>
                                        <span
                                            class="font-semibold text-green-600">{{ number_format($certificate->final_score, 2) }}%</span>
                                    </div>

                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-600">
                                            <i class="fas fa-fingerprint text-yellow-600 mr-1"></i>No. Sertifikat:
                                        </span>
                                        <span
                                            class="font-mono text-xs text-gray-900">{{ $certificate->certificate_number }}</span>
                                    </div>

                                    <!-- Actions -->
                                    <div class="pt-3 border-t flex gap-2">
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.certificates.show', $certificate) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                                            <i class="fas fa-eye"></i>Lihat
                                        </a>
                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.certificates.download', $certificate) }}"
                                            class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                                            <i class="fas fa-download"></i>Unduh
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="px-6 py-4 bg-gray-50 border-t">
                        {{ $certificates->links() }}
                    </div>
                @else
                    <div class="p-12 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-certificate text-gray-400 text-3xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Belum Ada Sertifikat</h3>
                        <p class="text-gray-500 mb-4">Selesaikan kursus dan lulus ujian untuk mendapatkan sertifikat.
                        </p>
                        <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg shadow-sm transition">
                            <i class="fas fa-book"></i>Lihat Kursus
                        </a>
                    </div>
                @endif
            </div>

            <!-- Info Box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
                <div class="flex items-start gap-3">
                    <div class="bg-blue-100 rounded-full p-3 flex-shrink-0">
                        <i class="fas fa-info-circle text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-2">Cara Mendapatkan Sertifikat</h4>
                        <ul class="text-sm text-gray-700 space-y-1">
                            <li><i class="fas fa-check text-blue-600 mr-2"></i>Selesaikan semua materi dalam kursus</li>
                            <li><i class="fas fa-check text-blue-600 mr-2"></i>Ikuti dan lulus semua ujian dengan nilai
                                minimal yang ditentukan</li>
                            <li><i class="fas fa-check text-blue-600 mr-2"></i>Sertifikat akan otomatis digenerate
                                setelah semua persyaratan terpenuhi</li>
                            <li><i class="fas fa-check text-blue-600 mr-2"></i>Anda dapat mengunduh sertifikat dalam
                                format PDF</li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
