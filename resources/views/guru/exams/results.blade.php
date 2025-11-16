<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <p class="text-sm text-gray-500 uppercase tracking-wide">Laporan Ujian</p>
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight flex items-center gap-2">
                    <i class="fas fa-chart-line text-blue-600"></i>
                    {{ $exam->title }}
                </h2>
                <p class="text-gray-600 text-sm">Kursus: {{ $exam->course->title }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('guru.reports.export-grades-excel', $exam) }}"
                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg shadow">
                    <i class="fas fa-file-excel mr-2"></i>Export Excel
                </a>
                <a href="{{ route('guru.reports.export-grades-pdf', $exam) }}"
                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-lg shadow">
                    <i class="fas fa-file-pdf mr-2"></i>Export PDF
                </a>
                <a href="{{ route('guru.exams.index') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-semibold rounded-lg border border-gray-300">
                    <i class="fas fa-arrow-left mr-2"></i>Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Statistics -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div class="border rounded-lg p-4 bg-blue-50 border-blue-100">
                            <p class="text-sm text-blue-600 font-semibold">Total Percobaan</p>
                            <p class="text-3xl font-bold text-blue-900 mt-2">{{ number_format($statistics['total_attempts']) }}</p>
                        </div>
                        <div class="border rounded-lg p-4 bg-green-50 border-green-100">
                            <p class="text-sm text-green-600 font-semibold">Selesai Dinilai</p>
                            <p class="text-3xl font-bold text-green-900 mt-2">
                                {{ number_format($statistics['completed_attempts']) }}
                            </p>
                        </div>
                        <div class="border rounded-lg p-4 bg-indigo-50 border-indigo-100">
                            <p class="text-sm text-indigo-600 font-semibold">Rata-rata Nilai</p>
                            <p class="text-3xl font-bold text-indigo-900 mt-2">
                                {{ number_format($statistics['average_score'], 2) }}%
                            </p>
                        </div>
                        <div class="border rounded-lg p-4 bg-purple-50 border-purple-100">
                            <p class="text-sm text-purple-600 font-semibold">Tingkat Kelulusan</p>
                            <p class="text-3xl font-bold text-purple-900 mt-2">
                                {{ number_format($statistics['pass_rate'], 1) }}%
                            </p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">Nilai Tertinggi</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($statistics['highest_score'], 2) }}%</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">Nilai Terendah</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ number_format($statistics['lowest_score'], 2) }}%</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <p class="text-sm text-gray-500">Nilai Minimal Kelulusan</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $exam->pass_score }}%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Daftar Percobaan Ujian</h3>
                            <p class="text-sm text-gray-500">Menampilkan {{ $attempts->total() }} percobaan (status selesai)</p>
                        </div>
                        <div class="text-sm text-gray-500">
                            Durasi: {{ $exam->duration_minutes }} menit • Total Soal: {{ $exam->questions->count() }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Peserta
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Nilai
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Poin
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Waktu Submit
                                    </th>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Durasi
                                    </th>
                                    <th scope="col" class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($attempts as $attempt)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $attempt->is_guest ? $attempt->guest_name : ($attempt->user->name ?? 'Tidak diketahui') }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $attempt->is_guest ? $attempt->guest_email : ($attempt->user->email ?? '-') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            @if (!is_null($attempt->score))
                                                <span class="font-semibold">{{ number_format($attempt->score, 2) }}%</span>
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
                                                    {{ $attempt->passed ? 'Lulus' : 'Tidak Lulus' }}
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
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
                                                <a href="{{ route('guru.exams.review-essays', $exam) }}"
                                                    class="text-indigo-600 hover:text-indigo-900">
                                                    Review Essay
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                            <i class="fas fa-inbox text-3xl mb-3 text-gray-300"></i>
                                            <p>Belum ada percobaan ujian yang selesai.</p>
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

