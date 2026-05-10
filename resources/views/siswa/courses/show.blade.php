<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-graduation-cap mr-2"></i>{{ $course->title }}
            </h2>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white text-gray-700 font-semibold rounded-lg border border-gray-300 hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-md rounded-lg">
                        <div class="p-6">
                            @if ($course->cover_image)
                                <img src="{{ Storage::url($course->cover_image) }}" alt="Cover"
                                    class="w-full h-64 object-cover rounded-lg mb-6 shadow-sm">
                            @else
                                <div
                                    class="w-full h-64 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mb-6 shadow-sm">
                                    <i class="fas fa-graduation-cap text-white text-8xl"></i>
                                </div>
                            @endif

                            <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h1>

                            <div class="flex flex-wrap gap-3 mb-6">
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-blue-100 text-blue-800 border border-blue-200">
                                    <i class="fas fa-code mr-2"></i>{{ $course->code }}
                                </span>
                                <span
                                    class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-2"></i>{{ $course->status_display }}
                                </span>
                            </div>

                            <div class="prose max-w-none">
                                <h3 class="text-lg font-bold text-gray-900 mb-3 flex items-center gap-2">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    {{ __('About This Course') }}
                                </h3>
                                <p class="text-gray-700 whitespace-pre-wrap leading-relaxed">
                                    {{ $course->description ?: __('No description') }}</p>
                            </div>

                            @if ($isEnrolled)
                                <div
                                    class="mt-8 p-6 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border-l-4 border-blue-500 shadow-sm">
                                    <h3 class="text-lg font-bold text-blue-900 mb-4 flex items-center gap-2">
                                        <i class="fas fa-chart-line text-blue-600"></i>
                                        Progress Anda
                                    </h3>

                                    <div class="flex items-center mb-4">
                                        <div class="flex-1 bg-blue-200 rounded-full h-4 mr-4">
                                            <div class="bg-blue-600 h-4 rounded-full transition-all duration-300"
                                                style="width: {{ $enrollment->progress }}%"></div>
                                        </div>
                                        <span
                                            class="text-xl font-bold text-blue-900">{{ $enrollment->progress }}%</span>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <span
                                            class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-semibold
                                            @if ($enrollment->status == 'active') bg-green-100 text-green-800 border border-green-200
                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800 border border-blue-200
                                            @else bg-red-100 text-red-800 border border-red-200 @endif">
                                            <i class="fas fa-circle mr-2 text-xs"></i>
                                            Status: {{ $enrollment->status_display }}
                                        </span>
                                        <span class="text-sm text-gray-700 flex items-center gap-2">
                                            <i class="fas fa-calendar text-gray-500"></i>
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
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">📚 {{ __('Learning Materials') }}
                                </h3>

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

                    <!-- Assignments (Only for enrolled students) -->
                    @if ($isEnrolled)
                        @php
                            $assignments = $course->assignments()->published()->orderBy('deadline', 'asc')->get();
                        @endphp
                        @if ($assignments->count() > 0)
                            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div class="p-6">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        <i class="fas fa-tasks text-orange-500 mr-2"></i>{{ __('Tugas') }}
                                    </h3>

                                    <div class="space-y-3">
                                        @foreach ($assignments as $assignment)
                                            <a href="{{ route(auth()->user()->getRolePrefix() . '.assignments.show', $assignment) }}"
                                                class="flex items-center p-4 border rounded-lg hover:bg-gray-50 transition-all">
                                                <div class="flex-shrink-0 mr-4">
                                                    @if ($assignment->getSubmissionForUser(auth()->user()))
                                                        <div
                                                            class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-check text-green-600"></i>
                                                        </div>
                                                    @elseif($assignment->isDeadlinePassed())
                                                        <div
                                                            class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-times text-red-600"></i>
                                                        </div>
                                                    @else
                                                        <div
                                                            class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                                                            <i class="fas fa-file-alt text-orange-600"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-gray-900">
                                                        {{ $assignment->title }}</p>
                                                    <div class="flex items-center gap-3 mt-1">
                                                        <span class="text-xs text-gray-500">
                                                            <i
                                                                class="fas fa-clock mr-1"></i>{{ $assignment->deadline->translatedFormat('d M Y, H:i') }}
                                                        </span>
                                                        @if ($assignment->getSubmissionForUser(auth()->user()))
                                                            <span class="text-xs text-green-600 font-semibold">Sudah
                                                                dikumpulkan</span>
                                                        @elseif($assignment->isDeadlinePassed())
                                                            <span class="text-xs text-red-600 font-semibold">Sudah
                                                                lewat</span>
                                                        @else
                                                            <span class="text-xs text-orange-600 font-semibold">Belum
                                                                dikumpulkan</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <i class="fas fa-chevron-right text-gray-400"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
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

                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.my-courses') }}"
                                        class="block w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded mb-2">
                                        <i class="fas fa-book mr-2"></i>{{ __('Go to My Courses') }}
                                    </a>

                                    <form
                                        action="{{ route(auth()->user()->getRolePrefix() . '.courses.unenroll', $course) }}"
                                        method="POST"
                                        onsubmit="return confirmDelete('{{ __('Are you sure you want to leave this class?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            <i class="fas fa-sign-out-alt mr-2"></i>{{ __('Leave Course') }}
                                        </button>
                                    </form>
                                </div>
                            @else
                                <div class="text-center">
                                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                                        {{ __('Enroll in this Course') }}</h3>

                                    @if ($course->isFull())
                                        <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                                            <i class="fas fa-exclamation-circle text-red-500 mr-2"></i>
                                            <span class="text-red-700 text-sm">{{ __('Course is full') }}</span>
                                        </div>
                                    @else
                                        <form
                                            action="{{ route(auth()->user()->getRolePrefix() . '.courses.enroll', $course) }}"
                                            method="POST">
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
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Course Information') }}</h3>

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
