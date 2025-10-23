<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Kelola Siswa - {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kode: {{ $course->code }}</p>
            </div>
            <a href="{{ auth()->user()->isAdmin() ? route('admin.courses.show', $course) : route('guru.courses.show', $course) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Add Student Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Tambah Siswa</h3>

                    @if ($availableStudents->count() > 0)
                        <form
                            action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.store', $course) : route('guru.courses.enrollments.store', $course) }}"
                            method="POST" class="flex gap-4">
                            @csrf
                            <select name="user_id" required
                                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Pilih Siswa</option>
                                @foreach ($availableStudents as $student)
                                    <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-user-plus mr-2"></i>Tambah
                            </button>
                        </form>
                    @else
                        <p class="text-gray-500">Semua siswa sudah terdaftar di kelas ini.</p>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Total Siswa</div>
                        <div class="text-3xl font-bold text-gray-900">{{ $enrollments->total() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Aktif</div>
                        <div class="text-3xl font-bold text-green-600">
                            {{ $enrollments->where('status', 'active')->count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Selesai</div>
                        <div class="text-3xl font-bold text-blue-600">
                            {{ $enrollments->where('status', 'completed')->count() }}</div>
                    </div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="text-sm text-gray-500 mb-1">Berhenti</div>
                        <div class="text-3xl font-bold text-red-600">
                            {{ $enrollments->where('status', 'dropped')->count() }}</div>
                    </div>
                </div>
            </div>

            <!-- Enrollments Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Daftar Siswa</h3>

                    @if ($enrollments->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Siswa</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Progress</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Terdaftar</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($enrollments as $enrollment)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $enrollment->student->name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $enrollment->student->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form
                                                    action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.update-status', [$course, $enrollment]) : route('guru.courses.enrollments.update-status', [$course, $enrollment]) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <select name="status" onchange="this.form.submit()"
                                                        class="text-xs rounded-full border-0
                                                            @if ($enrollment->status == 'active') bg-green-100 text-green-800
                                                            @elseif($enrollment->status == 'completed') bg-blue-100 text-blue-800
                                                            @else bg-red-100 text-red-800 @endif">
                                                        <option value="active"
                                                            {{ $enrollment->status == 'active' ? 'selected' : '' }}>
                                                            Aktif</option>
                                                        <option value="completed"
                                                            {{ $enrollment->status == 'completed' ? 'selected' : '' }}>
                                                            Selesai</option>
                                                        <option value="dropped"
                                                            {{ $enrollment->status == 'dropped' ? 'selected' : '' }}>
                                                            Berhenti</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-24 bg-gray-200 rounded-full h-2 mr-2">
                                                        <div class="bg-{{ $enrollment->progress_color }}-600 h-2 rounded-full"
                                                            style="width: {{ $enrollment->progress }}%"></div>
                                                    </div>
                                                    <span
                                                        class="text-sm text-gray-600">{{ $enrollment->progress }}%</span>
                                                </div>
                                                <form
                                                    action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.update-progress', [$course, $enrollment]) : route('guru.courses.enrollments.update-progress', [$course, $enrollment]) }}"
                                                    method="POST" class="mt-1">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="flex gap-1">
                                                        <input type="number" name="progress"
                                                            value="{{ $enrollment->progress }}" min="0"
                                                            max="100" class="w-16 text-xs rounded border-gray-300">
                                                        <button type="submit"
                                                            class="text-xs text-blue-600 hover:text-blue-900">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $enrollment->enrolled_at->format('d M Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <form
                                                    action="{{ auth()->user()->isAdmin() ? route('admin.courses.enrollments.destroy', [$course, $enrollment]) : route('guru.courses.enrollments.destroy', [$course, $enrollment]) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirmDelete('Yakin ingin menghapus siswa dari kelas ini?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $enrollments->links() }}
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Belum ada siswa terdaftar di kelas ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
