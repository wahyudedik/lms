<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $course->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.courses.edit', $course) }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('admin.courses.index') }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Course Details -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            @if ($course->cover_image)
                                <img src="{{ Storage::url($course->cover_image) }}" alt="Cover"
                                    class="w-full h-48 object-cover rounded mb-6">
                            @endif

                            <h3 class="text-2xl font-bold text-gray-900 mb-4">{{ $course->title }}</h3>

                            <div class="flex flex-wrap gap-4 mb-6">
                                <span
                                    class="px-3 py-1 text-sm font-semibold rounded-full 
                                    @if ($course->status == 'published') bg-green-100 text-green-800
                                    @elseif($course->status == 'draft') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $course->status_display }}
                                </span>
                                <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                                    <i class="fas fa-code mr-1"></i>{{ $course->code }}
                                </span>
                            </div>

                            <div class="prose max-w-none">
                                <h4 class="text-lg font-semibold text-gray-900 mb-2">Deskripsi</h4>
                                <p class="text-gray-700">{{ $course->description ?: 'Tidak ada deskripsi' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Enrolled Students -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Siswa Terdaftar</h3>
                                <a href="{{ route('admin.courses.enrollments', $course) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-cog mr-1"></i>Kelola
                                </a>
                            </div>

                            @if ($course->enrollments->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Siswa</th>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                    Progress</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach ($course->enrollments->take(5) as $enrollment)
                                                <tr>
                                                    <td class="px-4 py-2 text-sm text-gray-900">
                                                        {{ $enrollment->student->name }}</td>
                                                    <td class="px-4 py-2">
                                                        <span
                                                            class="px-2 text-xs font-semibold rounded-full
                                                            @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                            {{ $enrollment->status_display }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        <div class="flex items-center">
                                                            <div class="w-full bg-gray-200 rounded-full h-2 mr-2">
                                                                <div class="bg-blue-600 h-2 rounded-full"
                                                                    style="width: {{ $enrollment->progress }}%"></div>
                                                            </div>
                                                            <span
                                                                class="text-xs text-gray-600">{{ $enrollment->progress }}%</span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                                @if ($course->enrollments->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('admin.courses.enrollments', $course) }}"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                            Lihat semua {{ $course->enrollments->count() }} siswa
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-500 text-center py-4">Belum ada siswa terdaftar</p>
                            @endif
                        </div>
                    </div>

                    <!-- Materials -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Materi Pembelajaran</h3>
                                <a href="{{ route('admin.courses.materials.index', $course) }}"
                                    class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-cog mr-1"></i>Kelola Materi
                                </a>
                            </div>

                            @if ($course->materials()->published()->count() > 0)
                                <div class="space-y-3">
                                    @foreach ($course->materials()->published()->ordered()->take(5)->get() as $material)
                                        <a href="{{ route('admin.courses.materials.show', [$course, $material]) }}"
                                            class="flex items-center p-3 hover:bg-gray-50 rounded border">
                                            <i
                                                class="{{ $material->getFileIcon() }} {{ $material->getFileColorClass() }} text-2xl mr-3"></i>
                                            <div class="flex-1">
                                                <p class="text-sm font-medium text-gray-900">{{ $material->title }}</p>
                                                <p class="text-xs text-gray-500">{{ $material->type_display }}</p>
                                            </div>
                                            <i class="fas fa-chevron-right text-gray-400"></i>
                                        </a>
                                    @endforeach
                                </div>

                                @if ($course->materials()->published()->count() > 5)
                                    <div class="mt-4 text-center">
                                        <a href="{{ route('admin.courses.materials.index', $course) }}"
                                            class="text-blue-600 hover:text-blue-900 text-sm">
                                            Lihat semua {{ $course->materials()->published()->count() }} materi
                                        </a>
                                    </div>
                                @endif
                            @else
                                <p class="text-gray-500 text-center py-4">Belum ada materi dipublikasikan</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Course Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Informasi Kelas</h3>

                            <div class="space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Pengajar</p>
                                    <p class="text-base font-medium text-gray-900">{{ $course->instructor->name }}</p>
                                </div>

                                <div>
                                    <p class="text-sm text-gray-500">Kapasitas</p>
                                    <p class="text-base font-medium text-gray-900">
                                        @if ($course->max_students)
                                            {{ $course->enrollments->count() }} / {{ $course->max_students }} siswa
                                        @else
                                            {{ $course->enrollments->count() }} siswa (Tidak terbatas)
                                        @endif
                                    </p>
                                </div>

                                @if ($course->published_at)
                                    <div>
                                        <p class="text-sm text-gray-500">Dipublikasikan</p>
                                        <p class="text-base font-medium text-gray-900">
                                            {{ $course->published_at->format('d M Y') }}</p>
                                    </div>
                                @endif

                                <div>
                                    <p class="text-sm text-gray-500">Dibuat</p>
                                    <p class="text-base font-medium text-gray-900">
                                        {{ $course->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistik</h3>

                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Siswa Aktif</span>
                                    <span class="text-lg font-bold text-green-600">{{ $activeStudents }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Siswa Selesai</span>
                                    <span class="text-lg font-bold text-blue-600">{{ $completedStudents }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Total Siswa</span>
                                    <span
                                        class="text-lg font-bold text-gray-900">{{ $course->enrollments->count() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Aksi</h3>

                            <div class="space-y-2">
                                <form action="{{ route('admin.courses.toggle-status', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                        @if ($course->status == 'published')
                                            <i class="fas fa-archive mr-2"></i>Arsipkan
                                        @else
                                            <i class="fas fa-check mr-2"></i>Publikasikan
                                        @endif
                                    </button>
                                </form>

                                <a href="{{ route('admin.courses.enrollments', $course) }}"
                                    class="block w-full bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded text-center">
                                    <i class="fas fa-users mr-2"></i>Kelola Siswa
                                </a>

                                <form action="{{ route('admin.courses.destroy', $course) }}" method="POST"
                                    onsubmit="return confirmDelete('Yakin ingin menghapus kelas ini? Semua data terkait akan dihapus!')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-trash mr-2"></i>Hapus Kelas
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
