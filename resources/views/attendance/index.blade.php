<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i
                        class="fas fa-calendar-check mr-2 text-teal-600"></i>{{ __('Absensi — :title', ['title' => $material->title]) }}
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $course->title }} · {{ __('Kode') }}: {{ $course->code }}
                </p>
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <span
                    class="inline-flex items-center gap-2 px-3 py-2 bg-teal-50 text-teal-700 border border-teal-200 rounded-lg text-sm font-semibold">
                    <i class="fas fa-calendar-day"></i>
                    {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}
                </span>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.attendance.report', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-chart-bar"></i>
                    <span class="hidden sm:inline">{{ __('Report Absensi') }}</span>
                </a>
                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.index', $course) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                    <span class="hidden sm:inline">{{ __('Back') }}</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            @php
                $recordedCount = $students->filter(fn($row) => $row['attendance'] !== null)->count();
                $totalStudents = $students->count();
            @endphp

            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                        <div class="text-sm text-red-700">
                            <ul class="list-disc list-inside space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Filter Tanggal -->
            <div class="bg-white rounded-lg shadow-md border border-gray-200 p-4 sm:p-6">
                <div class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="attendance-date" class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-calendar text-gray-400 mr-1"></i>{{ __('Tanggal Absensi') }}
                        </label>
                        <input type="date" id="attendance-date" value="{{ $date }}"
                            onchange="window.location.href='{{ route(auth()->user()->getRolePrefix() . '.attendance.index', [$course, $material]) }}?date=' + encodeURIComponent(this.value);"
                            class="block px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent shadow-sm">
                    </div>
                    <div class="flex items-center gap-3 flex-wrap">
                        <span
                            class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-teal-100 text-teal-800">
                            <i class="fas fa-circle text-[6px] mr-2"></i>
                            {{ __('Tanggal aktif') }}: {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }}
                        </span>
                        @if ($date !== today()->format('Y-m-d'))
                            <a href="{{ route(auth()->user()->getRolePrefix() . '.attendance.index', [$course, $material]) }}"
                                class="text-sm font-semibold text-teal-600 hover:text-teal-800">
                                <i class="fas fa-redo mr-1"></i>{{ __('Kembali ke hari ini') }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Form Absensi -->
            <form method="POST"
                action="{{ route(auth()->user()->getRolePrefix() . '.attendance.save', [$course, $material]) }}">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">

                <div class="bg-white overflow-hidden shadow-md rounded-lg">
                    <div class="p-4 sm:p-6">
                        @if ($students->count() > 0)
                            <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase w-12">
                                                {{ __('No') }}
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">
                                                {{ __('Nama') }}
                                            </th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-green-600 uppercase">
                                                {{ __('Hadir') }}
                                            </th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-yellow-700 uppercase">
                                                {{ __('Sakit') }}
                                            </th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-blue-600 uppercase">
                                                {{ __('Ijin') }}
                                            </th>
                                            <th
                                                class="px-3 py-3 text-center text-xs font-semibold text-red-600 uppercase">
                                                {{ __('Keluar') }}
                                            </th>
                                            <th
                                                class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase min-w-[180px]">
                                                {{ __('Alasan') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach ($students as $index => $row)
                                            @php
                                                $student = $row['user'];
                                                $attendance = $row['attendance'];
                                                $selected =
                                                    $attendance->status ?? \App\Models\MaterialAttendance::STATUS_HADIR;
                                            @endphp
                                            <tr class="hover:bg-gray-50 transition-colors" x-data="{ status: '{{ $selected }}' }">
                                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $index + 1 }}
                                                </td>
                                                <td class="px-4 py-3">
                                                    <p class="text-sm font-semibold text-gray-900">{{ $student->name }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ $student->username }}</p>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <label class="inline-flex items-center justify-center">
                                                        <input type="radio"
                                                            name="records[{{ $index }}][status]" value="hadir"
                                                            class="peer sr-only" x-model="status">
                                                        <span
                                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-gray-200 bg-white text-gray-400 cursor-pointer transition-all duration-150 hover:border-gray-300 peer-checked:bg-green-600 peer-checked:border-green-600 peer-checked:text-white peer-checked:shadow-md"
                                                            title="{{ __('Hadir') }}">
                                                            <i class="fas fa-check text-sm"></i>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <label class="inline-flex items-center justify-center">
                                                        <input type="radio"
                                                            name="records[{{ $index }}][status]" value="sakit"
                                                            class="peer sr-only" x-model="status">
                                                        <span
                                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-gray-200 bg-white text-gray-400 cursor-pointer transition-all duration-150 hover:border-gray-300 peer-checked:bg-yellow-500 peer-checked:border-yellow-500 peer-checked:text-white peer-checked:shadow-md"
                                                            title="{{ __('Sakit') }}">
                                                            <i class="fas fa-check text-sm"></i>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <label class="inline-flex items-center justify-center">
                                                        <input type="radio"
                                                            name="records[{{ $index }}][status]" value="ijin"
                                                            class="peer sr-only" x-model="status">
                                                        <span
                                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-gray-200 bg-white text-gray-400 cursor-pointer transition-all duration-150 hover:border-gray-300 peer-checked:bg-blue-600 peer-checked:border-blue-600 peer-checked:text-white peer-checked:shadow-md"
                                                            title="{{ __('Ijin') }}">
                                                            <i class="fas fa-check text-sm"></i>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td class="px-3 py-3 text-center">
                                                    <label class="inline-flex items-center justify-center">
                                                        <input type="radio"
                                                            name="records[{{ $index }}][status]" value="keluar"
                                                            class="peer sr-only" x-model="status">
                                                        <span
                                                            class="inline-flex items-center justify-center w-9 h-9 rounded-lg border-2 border-gray-200 bg-white text-gray-400 cursor-pointer transition-all duration-150 hover:border-gray-300 peer-checked:bg-red-600 peer-checked:border-red-600 peer-checked:text-white peer-checked:shadow-md"
                                                            title="{{ __('Keluar') }}">
                                                            <i class="fas fa-check text-sm"></i>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td class="px-4 py-3">
                                                    <input type="hidden"
                                                        name="records[{{ $index }}][user_id]"
                                                        value="{{ $student->id }}">
                                                    <input type="text" name="records[{{ $index }}][alasan]"
                                                        value="{{ $attendance->alasan ?? '' }}" maxlength="500"
                                                        placeholder="—" :required="status !== 'hadir'"
                                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg text-sm shadow-sm focus:ring-2 focus:ring-teal-500 focus:border-transparent placeholder:text-gray-400">
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <p class="text-sm font-semibold text-gray-600">
                                    <i class="fas fa-clipboard-check text-teal-600 mr-1"></i>
                                    {{ $recordedCount }}/{{ $totalStudents }} {{ __('sudah diabsen') }}
                                    <span class="font-normal text-gray-500">({{ __('pada') }}
                                        {{ \Illuminate\Support\Carbon::parse($date)->format('d M Y') }})</span>
                                </p>
                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-save"></i>
                                    {{ __('Simpan Absensi') }}
                                </button>
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-user-slash text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-sm font-semibold mb-2">{{ __('Belum ada siswa yang dapat diabsen') }}
                                </p>
                                <p class="text-xs text-gray-400 mb-4">
                                    {{ __('Tidak ada siswa aktif yang terdaftar pada kursus/materi ini.') }}</p>
                                <a href="{{ route(auth()->user()->getRolePrefix() . '.courses.materials.index', $course) }}"
                                    class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
                                    <i class="fas fa-arrow-left mr-1"></i>{{ __('Kembali ke Materi') }}
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
