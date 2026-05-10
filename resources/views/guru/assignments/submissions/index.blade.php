<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-upload mr-2"></i>Pengumpulan - {{ $assignment->title }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">Nilai maks: {{ $assignment->max_score }}</p>
            </div>
            <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.assignments.show', [$assignment->course, $assignment]) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET"
                        action="{{ route(auth()->user()->getRolePrefix() . '.assignments.submissions.index', $assignment) }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-filter text-gray-400 mr-1"></i>Status
                            </label>
                            <select name="status"
                                class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="all" {{ request('status', 'all') == 'all' ? 'selected' : '' }}>Semua
                                    Status</option>
                                <option value="submitted" {{ request('status') == 'submitted' ? 'selected' : '' }}>
                                    Dikumpulkan</option>
                                <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>Terlambat
                                </option>
                                <option value="graded" {{ request('status') == 'graded' ? 'selected' : '' }}>Sudah
                                    Dinilai</option>
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

            <!-- Submissions Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    @if ($submissions->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Siswa
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Waktu Pengumpulan
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Nilai
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Aksi
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($submissions as $submission)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $submission->user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    {{ $submission->user->email }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($submission->status === 'graded')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Dinilai
                                                    </span>
                                                @elseif ($submission->status === 'late')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                        <i class="fas fa-clock mr-1"></i>Terlambat
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                        <i class="fas fa-upload mr-1"></i>Dikumpulkan
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $submission->submitted_at->format('d M Y, H:i') }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($submission->isGraded())
                                                    <div class="text-sm font-semibold text-gray-900">
                                                        {{ $submission->score }}/{{ $assignment->max_score }}
                                                    </div>
                                                    @if ($submission->final_score !== null && $submission->final_score != $submission->score)
                                                        <div class="text-xs text-orange-600">
                                                            Final: {{ number_format($submission->final_score, 2) }}
                                                        </div>
                                                    @endif
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <div class="flex justify-end gap-3">
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.assignments.submissions.show', [$assignment, $submission]) }}"
                                                        class="text-blue-600 hover:text-blue-800 font-semibold">
                                                        <i class="fas fa-eye mr-1"></i>Lihat
                                                    </a>
                                                    @if (!$submission->isGraded())
                                                        <a href="{{ route(auth()->user()->getRolePrefix() . '.assignments.submissions.show', [$assignment, $submission]) }}"
                                                            class="text-green-600 hover:text-green-800 font-semibold">
                                                            <i class="fas fa-pen mr-1"></i>Nilai
                                                        </a>
                                                    @endif
                                                    <a href="{{ route(auth()->user()->getRolePrefix() . '.assignments.submissions.download', [$assignment, $submission]) }}"
                                                        class="text-purple-600 hover:text-purple-800 font-semibold">
                                                        <i class="fas fa-download mr-1"></i>Unduh
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($submissions->hasPages())
                            <div class="mt-4">
                                {{ $submissions->links() }}
                            </div>
                        @endif
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold mb-2">Belum ada pengumpulan.</p>
                            <p class="text-sm text-gray-400">Siswa belum mengumpulkan tugas ini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
