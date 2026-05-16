<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-tasks mr-2"></i>Detail Tugas: {{ $assignment->title }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.edit', [$course, $assignment]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit"></i>
                    Edit
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.index', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Status & Deadline -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>Status Tugas
                            </h3>
                            <div class="mt-2 flex items-center gap-3">
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                    {{ $assignment->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    <i class="fas fa-{{ $assignment->is_published ? 'check' : 'clock' }} mr-1"></i>
                                    {{ $assignment->is_published ? 'Dipublikasikan' : 'Draft' }}
                                </span>
                                @if ($assignment->isDeadlinePassed())
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                                        <i class="fas fa-times-circle mr-1"></i>Deadline Terlewat
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        <i class="fas fa-clock mr-1"></i>
                                        @php
                                            $remaining = $assignment->getRemainingTime();
                                            $days = floor($remaining / 86400);
                                            $hours = floor(($remaining % 86400) / 3600);
                                        @endphp
                                        Sisa {{ $days }} hari {{ $hours }} jam
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.assignments.submissions.index', $assignment) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-list"></i>
                                Lihat Pengumpulan
                            </a>
                            <form
                                action="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.destroy', [$course, $assignment]) }}"
                                method="POST" class="inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-trash"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submission Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-users text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Total Terdaftar</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['total_enrolled'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-upload text-green-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Sudah Mengumpulkan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['submitted_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-check-double text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Sudah Dinilai</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['graded_count'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-md rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase">Belum Mengumpulkan</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $statistics['not_submitted_count'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Details -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-info-circle text-blue-600 mr-2"></i>Informasi Tugas
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">Nilai Maksimal</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $assignment->max_score }}</dd>
                        </div>

                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">Deadline</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                <i class="fas fa-calendar mr-1"></i>{{ $assignment->deadline->format('d M Y, H:i') }}
                            </dd>
                        </div>

                        <div class="p-3 bg-orange-50 rounded-lg border border-orange-100">
                            <dt class="text-xs font-semibold text-orange-700 mb-1">Kebijakan Keterlambatan</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                @if ($assignment->late_policy === 'reject')
                                    <i class="fas fa-ban text-red-500 mr-1"></i>Tolak
                                @elseif ($assignment->late_policy === 'allow')
                                    <i class="fas fa-check text-green-500 mr-1"></i>Izinkan
                                @else
                                    <i class="fas fa-percentage text-orange-500 mr-1"></i>Penalti
                                    ({{ $assignment->penalty_percentage }}%)
                                @endif
                            </dd>
                        </div>

                        <div class="p-3 bg-purple-50 rounded-lg border border-purple-100">
                            <dt class="text-xs font-semibold text-purple-700 mb-1">Dibuat Oleh</dt>
                            <dd class="text-sm font-semibold text-gray-900">{{ $assignment->creator->name }}</dd>
                        </div>

                        <div class="p-3 bg-indigo-50 rounded-lg border border-indigo-100">
                            <dt class="text-xs font-semibold text-indigo-700 mb-1">Tipe File Diizinkan</dt>
                            <dd class="text-sm font-semibold text-gray-900">
                                @if ($assignment->allowed_file_types && count($assignment->allowed_file_types) > 0)
                                    {{ implode(', ', array_map('strtoupper', $assignment->allowed_file_types)) }}
                                @else
                                    Semua tipe file
                                @endif
                            </dd>
                        </div>

                        @if ($assignment->material)
                            <div class="p-3 bg-teal-50 rounded-lg border border-teal-100">
                                <dt class="text-xs font-semibold text-teal-700 mb-1">Materi Terkait</dt>
                                <dd class="text-sm font-semibold text-gray-900">
                                    <i class="fas fa-link mr-1"></i>{{ $assignment->material->title }}
                                </dd>
                            </div>
                        @endif

                        <div class="p-3 bg-teal-50 rounded-lg border border-teal-100 md:col-span-2">
                            <dt class="text-xs font-semibold text-teal-700 mb-1">Kelompok Target</dt>
                            <dd class="flex flex-wrap gap-1 mt-1">
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
                            </dd>
                        </div>
                    </div>

                    @if ($assignment->description)
                        <div class="mt-4 p-3 bg-gray-50 rounded-lg border border-gray-100">
                            <dt class="text-xs font-semibold text-gray-700 mb-1">Deskripsi</dt>
                            <dd class="text-sm text-gray-900 whitespace-pre-line">{{ $assignment->description }}</dd>
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
