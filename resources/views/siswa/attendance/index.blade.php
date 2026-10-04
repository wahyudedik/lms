<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Absensi Saya</h2>
                <p class="mt-1 text-sm text-gray-500">Riwayat absensi kehadiran pada setiap materi kursus.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto space-y-6 max-w-7xl sm:px-6 lg:px-8">
            {{-- Filter kursus --}}
            @if (isset($courses) && $courses->count() > 0)
                <div class="p-4 bg-white rounded-xl shadow-sm sm:p-5">
                    <form method="GET" action="{{ route(auth()->user()->getRolePrefix() . '.attendance.index') }}"
                        class="flex flex-col gap-3 sm:flex-row sm:items-end">
                        <div class="flex-1">
                            <label for="course_id"
                                class="block mb-1 text-xs font-semibold text-gray-500 uppercase tracking-wide">Kursus</label>
                            <select id="course_id" name="course_id"
                                class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg focus:border-teal-500 focus:ring-teal-500 focus:outline-none focus:ring-1">
                                <option value="">Semua Kursus</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}"
                                        {{ ($filters['course_id'] ?? null) == $course->id ? 'selected' : '' }}>
                                        {{ $course->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-teal-600 rounded-lg hover:bg-teal-700">
                            <i class="fas fa-filter"></i>
                            Filter
                        </button>
                        @if (!empty($filters))
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.attendance.index') }}"
                                class="inline-flex items-center gap-1 px-3 py-2 text-sm font-medium text-gray-600 border border-gray-200 rounded-lg hover:bg-gray-50">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            @endif

            {{-- Tabel riwayat absensi --}}
            <div class="overflow-hidden bg-white rounded-xl shadow-sm">
                <div class="px-4 py-4 sm:px-6">
                    <h3 class="text-sm font-semibold text-gray-900">Riwayat Absensi</h3>
                    <p class="mt-0.5 text-xs text-gray-500">Terbaru ditampilkan di paling atas.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col"
                                    class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-6">
                                    Tanggal</th>
                                <th scope="col"
                                    class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-6">
                                    Kursus</th>
                                <th scope="col"
                                    class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-6">
                                    Materi</th>
                                <th scope="col"
                                    class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-6">
                                    Status</th>
                                <th scope="col"
                                    class="px-4 py-3 text-xs font-semibold tracking-wider text-left text-gray-500 uppercase sm:px-6">
                                    Alasan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse ($attendances as $attendance)
                                @php
                                    $material = $attendance->material;
                                    $badge =
                                        [
                                            \App\Models\MaterialAttendance::STATUS_HADIR =>
                                                'bg-green-100 text-green-800',
                                            \App\Models\MaterialAttendance::STATUS_SAKIT =>
                                                'bg-yellow-100 text-yellow-800',
                                            \App\Models\MaterialAttendance::STATUS_IJIN => 'bg-blue-100 text-blue-800',
                                            \App\Models\MaterialAttendance::STATUS_KELUAR => 'bg-red-100 text-red-800',
                                        ][$attendance->status] ?? 'bg-gray-100 text-gray-800';
                                @endphp
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap sm:px-6">
                                        {{ $attendance->attendance_date?->format('d M Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700 sm:px-6">
                                        {{ $material?->course?->title ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-900 sm:px-6">
                                        {{ $material?->title ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 sm:px-6">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 text-xs font-medium rounded-full {{ $badge }}">
                                            {{ $attendance->status_display }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-500 sm:px-6">
                                        {{ $attendance->alasan ?: '—' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-12 text-center sm:px-6">
                                        <i class="mb-2 text-3xl text-gray-300 fas fa-clipboard-check"></i>
                                        <p class="text-sm text-gray-500">
                                            @if (!empty($filters))
                                                Belum ada data absensi untuk filter yang dipilih.
                                            @else
                                                Belum ada data absensi untuk Anda.
                                            @endif
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
