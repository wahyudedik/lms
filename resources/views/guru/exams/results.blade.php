<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide font-semibold">Laporan Ujian</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i>
                    {{ $exam->title }}
                </h2>
                <p class="text-gray-600 text-sm mt-1">Kursus: {{ $exam->course->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-file-excel"></i>
                    Export Excel
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-file-pdf"></i>
                    Export PDF
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-chart-bar text-blue-600 mr-2"></i>Statistik Ujian
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Total Attempts -->
                        <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                            <div class="flex items-center">
                                <div class="p-3 bg-blue-100 rounded-lg mr-3">
                                    <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-blue-600 text-xs font-semibold mb-1">Total Percobaan</div>
                                    <div class="text-2xl font-bold text-blue-900">
                                        {{ number_format($statistics['total_attempts']) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Completed -->
                        <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-green-600">
                            <div class="flex items-center">
                                <div class="p-3 bg-green-100 rounded-lg mr-3">
                                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-green-600 text-xs font-semibold mb-1">Selesai Dinilai</div>
                                    <div class="text-2xl font-bold text-green-900">
                                        {{ number_format($statistics['completed_attempts']) }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Average Score -->
                        <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-indigo-600">
                            <div class="flex items-center">
                                <div class="p-3 bg-indigo-100 rounded-lg mr-3">
                                    <i class="fas fa-chart-line text-indigo-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-indigo-600 text-xs font-semibold mb-1">Rata-rata Nilai</div>
                                    <div class="text-2xl font-bold text-indigo-900">
                                        {{ number_format($statistics['average_score'], 2) }}%</div>
                                </div>
                            </div>
                        </div>

                        <!-- Pass Rate -->
                        <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-purple-600">
                            <div class="flex items-center">
                                <div class="p-3 bg-purple-100 rounded-lg mr-3">
                                    <i class="fas fa-trophy text-purple-600 text-2xl"></i>
                                </div>
                                <div>
                                    <div class="text-purple-600 text-xs font-semibold mb-1">Tingkat Kelulusan</div>
                                    <div class="text-2xl font-bold text-purple-900">
                                        {{ number_format($statistics['pass_rate'], 1) }}%</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                            <dt class="text-xs font-semibold text-green-700 mb-1">Nilai Tertinggi</dt>
                            <dd class="text-2xl font-bold text-green-900">
                                {{ number_format($statistics['highest_score'], 2) }}%</dd>
                        </div>
                        <div class="p-3 bg-red-50 rounded-lg border border-red-100">
                            <dt class="text-xs font-semibold text-red-700 mb-1">Nilai Terendah</dt>
                            <dd class="text-2xl font-bold text-red-900">
                                {{ number_format($statistics['lowest_score'], 2) }}%</dd>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                            <dt class="text-xs font-semibold text-blue-700 mb-1">Nilai Minimal Kelulusan</dt>
                            <dd class="text-2xl font-bold text-blue-900">{{ $exam->pass_score }}%</dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">
                                <i class="fas fa-list text-purple-600 mr-2"></i>Daftar Percobaan Ujian
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">Menampilkan {{ $attempts->total() }} percobaan
                                (status selesai)</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-clock text-gray-400 mr-1"></i>Durasi: {{ $exam->duration_minutes }} menit
                            •
                            <i class="fas fa-list text-gray-400 mr-1"></i>Total Soal: {{ $exam->questions->count() }}
                        </div>
                    </div>

                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Peserta
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Nilai
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Poin
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Waktu Submit
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Durasi
                                    </th>
                                    <th scope="col" class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($attempts as $attempt)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-semibold text-gray-900">
                                                {{ $attempt->is_guest ? $attempt->guest_name : $attempt->user->name ?? 'Tidak diketahui' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attempt->is_guest ? $attempt->guest_email : $attempt->user->email ?? '-' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if (!is_null($attempt->score))
                                                <span class="font-bold">{{ number_format($attempt->score, 2) }}%</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if ($attempt->total_points_possible)
                                                {{ number_format($attempt->total_points_earned, 2) }} /
                                                {{ number_format($attempt->total_points_possible, 2) }}
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if ($attempt->status === 'graded')
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $attempt->passed ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    <i
                                                        class="fas fa-{{ $attempt->passed ? 'check' : 'times' }} mr-1"></i>
                                                    {{ $attempt->passed ? 'Lulus' : 'Tidak Lulus' }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ ucfirst(str_replace('_', ' ', $attempt->status)) }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y H:i') : '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            @if ($attempt->time_spent)
                                                {{ gmdate('H:i:s', $attempt->time_spent) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            @if ($exam->questions->where('type', 'essay')->count() > 0)
                                                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $exam) }}"
                                                    class="text-indigo-600 hover:text-indigo-800 font-semibold">
                                                    Review Essay
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <div
                                                    class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                    <i class="fas fa-inbox text-gray-400 text-2xl"></i>
                                                </div>
                                                <p class="text-sm font-semibold">Belum ada percobaan ujian yang
                                                    selesai.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $attempts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
