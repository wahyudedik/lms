<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-tasks mr-2"></i>Tugas - {{ $course->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Kode: {{ $course->code }}</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.create', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-plus"></i>
                    Buat Tugas
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.show', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET"
                        action="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.index', $course) }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>Cari
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Judul tugas..."
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-filter text-gray-400 mr-1"></i>Status
                            </label>
                            <select name="status"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Semua Status</option>
                                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>
                                    Dipublikasikan
                                </option>
                                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft
                                </option>
                            </select>
                        </div>

                        <div class="flex items-end">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-filter"></i>
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Assignments Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    @if ($assignments->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Tugas
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Deadline
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Pengumpulan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($assignments as $assignment)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $assignment->title }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    Nilai maks: {{ $assignment->max_score }}
                                                </div>
                                                <div class="mt-1 flex flex-wrap gap-1">
                                                    @if ($assignment->courseGroups && $assignment->courseGroups->count() > 0)
                                                        @foreach ($assignment->courseGroups as $group)
                                                            <span
                                                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800"><i
                                                                    class="fas fa-users mr-1"></i>{{ $group->name }}</span>
                                                        @endforeach
                                                    @else
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700"><i
                                                                class="fas fa-globe mr-1"></i>Semua Siswa</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <i class="fas fa-calendar text-gray-400 mr-1"></i>
                                                    {{ $assignment->deadline->format('d M Y, H:i') }}
                                                </div>
                                                @if ($assignment->isDeadlinePassed())
                                                    <span class="text-xs text-red-600 font-semibold">Sudah lewat</span>
                                                @else
                                                    <span class="text-xs text-green-600 font-semibold">Masih
                                                        aktif</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                    <i class="fas fa-upload mr-1"></i>
                                                    {{ $assignment->submissions_count }} pengumpulan
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                                    {{ $assignment->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    <i
                                                        class="fas fa-{{ $assignment->is_published ? 'check' : 'clock' }} mr-1"></i>
                                                    {{ $assignment->is_published ? 'Dipublikasikan' : 'Draft' }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <div class="flex justify-end gap-3">
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.show', [$course, $assignment]) }}"
                                                        class="text-blue-600 hover:text-blue-800 font-semibold">
                                                        <i class="fas fa-eye mr-1"></i>Lihat
                                                    </a>
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.edit', [$course, $assignment]) }}"
                                                        class="text-green-600 hover:text-green-800 font-semibold">
                                                        <i class="fas fa-edit mr-1"></i>Edit
                                                    </a>
                                                    <form
                                                        action="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.toggle-status', [$course, $assignment]) }}"
                                                        method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit"
                                                            class="text-purple-600 hover:text-purple-800 font-semibold">
                                                            <i
                                                                class="fas fa-{{ $assignment->is_published ? 'eye-slash' : 'eye' }} mr-1"></i>
                                                            {{ $assignment->is_published ? 'Unpublish' : 'Publish' }}
                                                        </button>
                                                    </form>
                                                    <form
                                                        action="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.destroy', [$course, $assignment]) }}"
                                                        method="POST" class="inline delete-form">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-800 font-semibold">
                                                            <i class="fas fa-trash mr-1"></i>Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($assignments->hasPages())
                            <div class="mt-4">
                                {{ $assignments->links() }}
                            </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold mb-2">Belum ada tugas.</p>
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.create', $course) }}"
                                class="mt-4 inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                Buat Tugas Pertama
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.querySelectorAll('.delete-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'Hapus Tugas?',
                        text: 'Tugas yang dihapus tidak dapat dikembalikan!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                        }
                    });
                });
            });
        </script>
    @endpush
</x-app-layout>
