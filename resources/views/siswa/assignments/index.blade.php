<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <i class="fas fa-tasks mr-2"></i>Tugas Saya
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
                                            Kursus
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Deadline
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Status
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
                                    @foreach ($assignments as $assignment)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $assignment->title }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Nilai maks: {{ $assignment->max_score }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $assignment->course->title }}
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
                                                @if (!$assignment->user_submission)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-800">
                                                        <i class="fas fa-minus-circle mr-1"></i>Belum Dikumpulkan
                                                    </span>
                                                @elseif ($assignment->user_submission->status === 'submitted')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                                        <i class="fas fa-check-circle mr-1"></i>Dikumpulkan
                                                    </span>
                                                @elseif ($assignment->user_submission->status === 'late')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-orange-100 text-orange-800">
                                                        <i class="fas fa-clock mr-1"></i>Terlambat
                                                    </span>
                                                @elseif ($assignment->user_submission->status === 'graded')
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <i class="fas fa-star mr-1"></i>Dinilai
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($assignment->user_submission && $assignment->user_submission->status === 'graded')
                                                    <span class="text-sm font-bold text-green-700">
                                                        {{ $assignment->user_submission->final_score ?? $assignment->user_submission->score }}
                                                        / {{ $assignment->max_score }}
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                                <a href="{{ route(auth()->user()->getRolePrefix() . '.', $assignment) }}"
                                                    class="text-blue-600 hover:text-blue-800 font-semibold">
                                                    <i class="fas fa-eye mr-1"></i>Lihat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-tasks text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-lg font-semibold mb-2">Belum ada tugas.</p>
                            <p class="text-sm text-gray-400">Tugas dari kursus yang Anda ikuti akan muncul di sini.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
