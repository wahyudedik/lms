<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-chart-bar mr-2"></i>{{ __('Exam Results: :title', ['title' => $exam->title]) }}
            </h2>
            <div class="flex gap-2">
                @if($attempts->count() > 0)
                    <a href="{{ route('admin.exams.export-results', $exam) }}"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-download"></i>
                        {{ __('Export Excel') }}
                    </a>
                @endif
                <a href="{{ route('admin.exams.show', $exam) }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- Total Attempts -->
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-blue-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-blue-100 rounded-lg mr-3">
                            <i class="fas fa-users text-blue-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-blue-600 text-xs font-semibold mb-1">{{ __('Total Percobaan') }}</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $statistics['total_attempts'] }}</div>
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
                            <div class="text-green-600 text-xs font-semibold mb-1">{{ __('Selesai Dinilai') }}</div>
                            <div class="text-2xl font-bold text-green-900">{{ $statistics['completed_attempts'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Average Score -->
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-purple-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg mr-3">
                            <i class="fas fa-chart-line text-purple-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-purple-600 text-xs font-semibold mb-1">{{ __('Average Score') }}</div>
                            <div class="text-2xl font-bold text-purple-900">
                                {{ $statistics['average_score'] ? number_format($statistics['average_score'], 1) . '%' : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Highest Score -->
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-orange-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-orange-100 rounded-lg mr-3">
                            <i class="fas fa-arrow-up text-orange-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-orange-600 text-xs font-semibold mb-1">{{ __('Highest Score') }}</div>
                            <div class="text-2xl font-bold text-orange-900">
                                {{ $statistics['highest_score'] ? number_format($statistics['highest_score'], 1) . '%' : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lowest Score -->
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-red-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-red-100 rounded-lg mr-3">
                            <i class="fas fa-arrow-down text-red-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-red-600 text-xs font-semibold mb-1">{{ __('Lowest Score') }}</div>
                            <div class="text-2xl font-bold text-red-900">
                                {{ $statistics['lowest_score'] ? number_format($statistics['lowest_score'], 1) . '%' : '-' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pass Rate -->
                <div class="bg-white rounded-lg p-4 shadow-md border-l-4 border-teal-600">
                    <div class="flex items-center">
                        <div class="p-3 bg-teal-100 rounded-lg mr-3">
                            <i class="fas fa-percentage text-teal-600 text-2xl"></i>
                        </div>
                        <div>
                            <div class="text-teal-600 text-xs font-semibold mb-1">{{ __('Tingkat Kelulusan') }}</div>
                            <div class="text-2xl font-bold text-teal-900">{{ number_format($statistics['pass_rate'], 1) }}%</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-list text-indigo-600 mr-2"></i>{{ __('Attempts List') }}
                    </h3>

                    @if ($attempts->count() > 0)
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Siswa') }}
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Dimulai') }}
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Dikumpulkan') }}
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Waktu') }}
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Score') }}
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Status') }}
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            {{ __('Pelanggaran') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($attempts as $attempt)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                @php
                                                    $attemptUser = $attempt->user;
                                                    $isGuest = $attempt->is_guest;
                                                    $displayName = $isGuest
                                                        ? ($attempt->guest_name ?? __('Guest'))
                                                        : ($attemptUser->name ?? __('Tidak diketahui'));
                                                    $displayEmail = $isGuest
                                                        ? ($attempt->guest_email ?? __('-'))
                                                        : ($attemptUser->email ?? __('-'));
                                                    $avatarUrl = $attemptUser?->profile_photo_url ?? asset('images/avatars/default-avatar.png');
                                                @endphp
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover"
                                                            src="{{ $avatarUrl }}"
                                                            alt="{{ $displayName }}">
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-semibold text-gray-900">
                                                            {{ $displayName }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $displayEmail }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->started_at ? $attempt->started_at->format('d M Y') : '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $attempt->started_at ? $attempt->started_at->format('H:i') : '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y') : '-' }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('H:i') : '' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    {{ $attempt->formatted_time_spent }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($attempt->score !== null)
                                                    <div class="text-lg font-bold {{ $attempt->passed ? 'text-green-600' : 'text-red-600' }}">
                                                        {{ number_format($attempt->score, 1) }}%
                                                    </div>
                                                    <div class="text-xs text-gray-500">
                                                        {{ number_format($attempt->total_points_earned, 1) }}/{{ number_format($attempt->total_points_possible, 1) }} poin
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                {!! $attempt->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if ($attempt->tab_switches > 0 || $attempt->fullscreen_exits > 0)
                                                    <div class="text-sm text-red-600">
                                                        @if ($attempt->tab_switches > 0)
                                                            <div><i class="fas fa-window-restore mr-1"></i>Tab: {{ $attempt->tab_switches }}x</div>
                                                        @endif
                                                        @if ($attempt->fullscreen_exits > 0)
                                                            <div><i class="fas fa-expand mr-1"></i>FS: {{ $attempt->fullscreen_exits }}x</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <i class="fas fa-check mr-1"></i>{{ __('Bersih') }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $attempts->links() }}
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center text-gray-500 py-12">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-lg font-semibold">{{ __('No students have taken this exam yet.') }}</p>
                            <p class="text-sm text-gray-400 mt-1">{{ __('Results will appear here once students complete the exam.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
