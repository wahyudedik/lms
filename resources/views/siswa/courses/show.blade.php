<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <a href="{{ route('siswa.courses.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if ($course->cover_image)
                                <img src="{{ Storage::url($course->cover_image) }}" alt="Cover"
                                    class="w-full h-64 object-cover rounded mb-6">
                            @else
                                <div
                                    class="w-full h-64 bg-gradient-to-br from-blue-500 to-purple-600 rounded flex items-center justify-center mb-6">
                                    <i class="fas fa-graduation-cap text-white text-8xl"></i>
                                </div>
                            @endif

                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>

                            <div class="flex flex-wrap gap-3 mb-6">
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-code mr-1"></i>{{ $course->code }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle mr-1"></i>{{ $course->status_display }}
                                </span>
                            </div>

                            <div class="prose max-w-none">
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">Tentang Kelas Ini</h3>
                                <p class="text-gray-700 whitespace-pre-wrap">
                                    {{ $course->description ?: 'Tidak ada deskripsi' }}</p>
                            </div>

                            @if ($isEnrolled)
                                <div class="mt-8 p-6 bg-blue-50 rounded-lg">
                                    <h3 class="text-lg font-semibold text-blue-900 mb-3">Progress Anda</h3>

                                    <div class="flex items-center mb-3">
                                        <div class="flex-1 bg-blue-200 rounded-full h-4 mr-3">
                                            <div class="bg-blue-600 h-4 rounded-full"
                                                style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        <span
                                            class="text-lg font-bold text-blue-900">{{ $enrollment->progress }}%</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span
                                            class="px-3 py-1 text-sm font-semibold rounded-full
                                            @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                            @else bg-red-100 text-red-800 @endif">
                                            Status: {{ $enrollment->status_display }}
                                        </span>
                                        <span class="text-sm text-gray-600">
                                            Bergabung: {{ $enrollment->enrolled_at->format('d M Y') }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Materials (Only for enrolled students) -->
                    @if ($isEnrolled && $course->materials()->published()->count() > 0)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📚 Materi Pembelajaran</h3>

                                <div class="space-y-3">
                                    @foreach ($course->materials()->published()->ordered()->get() as $material)
                                        <div class="border rounded-lg overflow-hidden">
                                            <div class="flex items-center p-4 hover:bg-gray-50 cursor-pointer"
                                                onclick="document.getElementById('material{{ $material->id }}').classList.toggle('hidden')">
                                                <i
                                                    class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-3xl mr-4"></i>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-gray-900">{{ $material->title }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $material->type_display }} •
                                                        {{ $material->created_at->diffForHumans() }}</p>
                                                </div>
                                                <i class="fas fa-chevron-down text-gray-400"></i>
                                            </div>

                                            <!-- Material Content -->
                                            <div id="material{{ $material->id }}"
                                                class="hidden p-6 bg-gray-50 border-t">
                                                @if ($material->description)
                                                    <p class="text-gray-700 mb-4">{{ $material->description }}</p>
                                                @endif

                                                @if ($material->type === 'file' || $material->type === 'video')
                                                    <a href="{{ $material->getFileUrl() }}" download
                                                        class="inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                                                        <i class="fas fa-download mr-2"></i>Download
                                                    </a>
                                                @elseif($material->type === 'youtube' && $material->getEmbedUrl())
                                                    <div class="mb-4">
                                                        <iframe src="{{ $material->getEmbedUrl() }}" frameborder="0"
                                                            allowfullscreen class="w-full h-96 rounded"></iframe>
                                                    </div>
                                                @elseif($material->type === 'link')
                                                    <a href="{{ $material->url }}" target="_blank"
                                                        class="inline-flex items-center bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4">
                                                        <i class="fas fa-external-link-alt mr-2"></i>Buka Link
                                                    </a>
                                                @endif

                                                <!-- Comments -->
                                                <div class="mt-6 pt-6 border-t">
                                                    <h4 class="font-semibold mb-3">💬 Diskusi</h4>
                                                    <form action="{{ route('materials.comments.store', $material) }}"
                                                        method="POST" class="mb-4">
                                                        @csrf
                                                        <textarea name="comment" rows="2" required placeholder="Tulis komentar..."
                                                            class="w-full rounded-md border-gray-300 text-sm"></textarea>
                                                        <button type="submit"
                                                            class="mt-2 bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                                                            Kirim
                                                        </button>
                                                    </form>

                                                    <div class="space-y-3">
                                                        @foreach ($material->comments as $comment)
                                                            <div class="border-l-2 border-blue-500 pl-3">
                                                                <div class="flex items-start">
                                                                    <img src="{{ $comment->user->profile_photo_url }}"
                                                                        class="w-6 h-6 rounded-full mr-2">
                                                                    <div>
                                                                        <p class="text-xs font-semibold">
                                                                            {{ $comment->user->name }}</p>
                                                                        <p class="text-xs text-gray-700">
                                                                            {{ $comment->comment }}</p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Enrollment Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if ($isEnrolled)
                                <div class="text-center">
                                    <div class="mb-4">
                                        <i class="fas fa-check-circle text-green-500 text-5xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">Anda Sudah Terdaftar</h3>
                                    <p class="text-gray-600 mb-4">Anda adalah bagian dari kelas ini</p>

                                    <a href="{{ route('siswa.courses.my-courses') }}"
                                        class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded mb-2">
                                        <i class="fas fa-book mr-2"></i>Ke Kelas Saya
                                    </a>

                                    <form action="{{ route('siswa.courses.unenroll', $course) }}" method="POST"
                                        onsubmit="return confirmDelete('Yakin ingin keluar dari kelas ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            <i class="fas fa-sign-out-alt mr-2"></i>Keluar dari Kelas
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="text-center">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar ke Kelas Ini</h3>

                                    @if ($course->isFull())
                                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                            <span class="text-red-700 text-sm">Kelas sudah penuh</span>
                                        </div>
                                    @else
                                        <form action="{{ route('siswa.courses.enroll', $course) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-3 px-4 rounded">
                                                <i class="fas fa-user-plus mr-2"></i>Daftar Sekarang
                                            </button>
                                        </form>
                                        <p class="text-xs text-gray-500 mt-2">Gratis - Mulai belajar sekarang</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Course Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kelas</h3>

                            <div class="space-y-4">
                                <div class="flex items-start">
                                    <i class="fas fa-user-tie text-gray-400 mr-3 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Pengajar</p>
                                        <p class="font-medium text-gray-900">{{ $course->instructor->name }}</p>
                                    </div>
                                </div>

                                <div class="flex items-start">
                                    <i class="fas fa-users text-gray-400 mr-3 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Siswa Terdaftar</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $activeStudentsCount }} siswa
                                            @if ($course->max_students)
                                                / {{ $course->max_students }}
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if ($course->max_students)
                                    <div class="flex items-start">
                                        <i class="fas fa-percentage text-gray-400 mr-3 mt-1"></i>
                                        <div>
                                            <p class="text-sm text-gray-500">Kapasitas</p>
                                            <div class="flex-1 bg-gray-200 rounded-full h-2 mt-1">
                                                <div class="bg-blue-600 h-2 rounded-full"
                                                    style="width: {{ ($activeStudentsCount / $course->max_students) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-start">
                                    <i class="fas fa-calendar text-gray-400 mr-3 mt-1"></i>
                                    <div>
                                        <p class="text-sm text-gray-500">Dipublikasikan</p>
                                        <p class="font-medium text-gray-900">
                                            {{ $course->published_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
