<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-book-reader text-indigo-600 mr-2"></i>Detail Materi
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.materials.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-sm transition">
                <i class="fas fa-arrow-left"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Material Header -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 border-l-4 border-indigo-500 p-6">
                <div class="flex items-start justify-between gap-4 mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ $material->title }}</h1>

                        <div class="flex items-center gap-3 mb-4 flex-wrap">
                            @if ($material->type == 'pdf')
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-blue-100 text-blue-800">
                                    <i class="fas fa-file-pdf"></i>PDF Document
                                </span>
                            @elseif($material->type == 'video')
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    <i class="fas fa-video"></i>Video
                                </span>
                            @elseif($material->type == 'youtube')
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800">
                                    <i class="fab fa-youtube"></i>YouTube Video
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800">
                                    <i class="fas fa-link"></i>External Link
                                </span>
                            @endif
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">
                                    <i class="fas fa-book text-indigo-600 mr-1"></i>
                                    <span class="font-semibold">Kursus:</span>
                                </span>
                                <span class="text-gray-900">{{ $material->course->title }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">
                                    <i class="fas fa-user text-indigo-600 mr-1"></i>
                                    <span class="font-semibold">Instruktur:</span>
                                </span>
                                <span class="text-gray-900">{{ $material->course->instructor->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-600">
                                    <i class="fas fa-calendar text-indigo-600 mr-1"></i>
                                    <span class="font-semibold">Dipublikasikan:</span>
                                </span>
                                <span class="text-gray-900">{{ $material->created_at->format('d M Y, H:i') }}</span>
                            </div>
                            @if ($material->updated_at != $material->created_at)
                                <div>
                                    <span class="text-gray-600">
                                        <i class="fas fa-clock text-indigo-600 mr-1"></i>
                                        <span class="font-semibold">Diperbarui:</span>
                                    </span>
                                    <span class="text-gray-900">{{ $material->updated_at->format('d M Y, H:i') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($material->description)
                    <div class="mt-4 pt-4 border-t">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-align-left text-gray-400 mr-1"></i>Deskripsi
                        </h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $material->description }}</p>
                    </div>
                @endif
            </div>

            <!-- Material Content -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b">
                    <h3 class="text-lg font-bold text-gray-900">
                        <i class="fas fa-file-alt text-indigo-600 mr-2"></i>Konten Materi
                    </h3>
                </div>

                <div class="p-6">
                    @if ($material->type == 'pdf')
                        <div class="space-y-4">
                            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="bg-blue-100 rounded-full p-3">
                                        <i class="fas fa-file-pdf text-blue-600 text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">PDF Document</h4>
                                        <p class="text-sm text-gray-600">Klik tombol di bawah untuk membuka atau
                                            mengunduh file PDF</p>
                                    </div>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ Storage::url($material->file_path) }}" target="_blank"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-sm transition">
                                    <i class="fas fa-external-link-alt"></i>Buka PDF
                                </a>
                                <a href="{{ Storage::url($material->file_path) }}" download
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition">
                                    <i class="fas fa-download"></i>Unduh PDF
                                </a>
                            </div>
                        </div>
                    @elseif($material->type == 'video')
                        <div class="space-y-4">
                            <div class="bg-gray-900 rounded-lg overflow-hidden">
                                <video controls class="w-full">
                                    <source src="{{ Storage::url($material->file_path) }}" type="video/mp4">
                                    Browser Anda tidak mendukung video player.
                                </video>
                            </div>
                            <a href="{{ Storage::url($material->file_path) }}" download
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition">
                                <i class="fas fa-download"></i>Unduh Video
                            </a>
                        </div>
                    @elseif($material->type == 'youtube')
                        <div class="space-y-4">
                            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden">
                                <iframe class="w-full h-full"
                                    src="https://www.youtube.com/embed/{{ $material->youtube_id }}" frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                            <a href="https://www.youtube.com/watch?v={{ $material->youtube_id }}" target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-sm transition">
                                <i class="fab fa-youtube"></i>Buka di YouTube
                            </a>
                        </div>
                    @elseif($material->type == 'link')
                        <div class="space-y-4">
                            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <div class="bg-green-100 rounded-full p-3">
                                        <i class="fas fa-link text-green-600 text-2xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">External Link</h4>
                                        <p class="text-sm text-gray-600 break-all">{{ $material->external_link }}</p>
                                    </div>
                                </div>
                            </div>
                            <a href="{{ $material->external_link }}" target="_blank"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-sm transition">
                                <i class="fas fa-external-link-alt"></i>Buka Link
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section (if enabled) -->
            @if ($material->allow_comments)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b">
                        <h3 class="text-lg font-bold text-gray-900">
                            <i class="fas fa-comments text-indigo-600 mr-2"></i>Komentar & Diskusi
                        </h3>
                    </div>

                    <div class="p-6">
                        <!-- Add Comment Form -->
                        <form action="{{ route(auth()->user()->getRolePrefix() . '.materials.comment', $material) }}" method="POST"
                            class="mb-6">
                            @csrf
                            <div class="mb-3">
                                <label for="comment" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-comment text-gray-400 mr-1"></i>Tulis Komentar
                                </label>
                                <textarea name="comment" id="comment" rows="3"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    placeholder="Tulis komentar atau pertanyaan Anda..." required></textarea>
                            </div>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-sm transition">
                                <i class="fas fa-paper-plane"></i>Kirim Komentar
                            </button>
                        </form>

                        <!-- Comments List -->
                        @if ($material->comments && $material->comments->count() > 0)
                            <div class="space-y-4">
                                @foreach ($material->comments as $comment)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div class="flex items-start gap-3">
                                            <div
                                                class="bg-indigo-100 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-user text-indigo-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2 mb-1">
                                                    <span
                                                        class="font-semibold text-gray-900">{{ $comment->user->name }}</span>
                                                    <span
                                                        class="text-xs text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                                </div>
                                                <p class="text-gray-700">{{ $comment->comment }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div
                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-comments text-gray-400 text-2xl"></i>
                                </div>
                                <p class="text-gray-500">Belum ada komentar. Jadilah yang pertama berkomentar!</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
