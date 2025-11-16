<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Exam Results: :title', ['title' => $exam->title]) }}
            </h2>
            <a href="{{ route('admin.exams.show', $exam) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                                <i class="fas fa-users text-white text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Total Percobaan</dt>
                                    <dd class="text-lg font-semibold text-gray-900">{{ $statistics['total_attempts'] }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                                <i class="fas fa-check-circle text-white text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Selesai Dinilai</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $statistics['completed_attempts'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                                <i class="fas fa-chart-line text-white text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Average Score') }}</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $statistics['average_score'] ? number_format($statistics['average_score'], 2) . '%' : '-' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-purple-500 rounded-md p-3">
                                <i class="fas fa-arrow-up text-white text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Highest Score') }}</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $statistics['highest_score'] ? number_format($statistics['highest_score'], 2) . '%' : '-' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-500 rounded-md p-3">
                                <i class="fas fa-arrow-down text-white text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">{{ __('Lowest Score') }}</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ $statistics['lowest_score'] ? number_format($statistics['lowest_score'], 2) . '%' : '-' }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-teal-500 rounded-md p-3">
                                <i class="fas fa-percentage text-white text-2xl"></i>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 truncate">Tingkat Kelulusan</dt>
                                    <dd class="text-lg font-semibold text-gray-900">
                                        {{ number_format($statistics['pass_rate'], 2) }}%
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attempts Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Attempts List') }}</h3>

                    @if ($attempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Siswa
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Dimulai
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Dikumpulkan
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Waktu
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            {{ __('Score') }}
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Status
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Pelanggaran
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($attempts as $attempt)
                                        <tr>
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
                                                    <img class="h-8 w-8 rounded-full object-cover"
                                                        src="{{ $avatarUrl }}"
                                                        alt="{{ $displayName }}">
                                                    <div class="ml-3">
                                                        <div class="text-sm font-medium text-gray-900">
                                                            {{ $displayName }}
                                                        </div>
                                                        <div class="text-sm text-gray-500">
                                                            {{ $displayEmail }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->started_at ? $attempt->started_at->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y, H:i') : '-' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $attempt->formatted_time_spent }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-gray-900">
                                                    @if ($attempt->score !== null)
                                                        <span
                                                            class="text-{{ $attempt->passed ? 'green' : 'red' }}-600">
                                                            {{ number_format($attempt->score, 2) }}%
                                                        </span>
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                                @if ($attempt->score !== null)
                                                    <div class="text-xs text-gray-500">
                                                        {{ number_format($attempt->total_points_earned, 2) }}/{{ number_format($attempt->total_points_possible, 2) }}
                                                        poin
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {!! $attempt->status_badge !!}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if ($attempt->tab_switches > 0 || $attempt->fullscreen_exits > 0)
                                                    <div class="text-sm text-red-600">
                                                        @if ($attempt->tab_switches > 0)
                                                            <div><i class="fas fa-window-restore mr-1"></i>Tab:
                                                                {{ $attempt->tab_switches }}x</div>
                                                        @endif
                                                        @if ($attempt->fullscreen_exits > 0)
                                                            <div><i class="fas fa-expand mr-1"></i>FS:
                                                                {{ $attempt->fullscreen_exits }}x</div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-sm text-green-600">
                                                        <i class="fas fa-check-circle mr-1"></i>Bersih
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
                        <div class="text-center py-8">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500 text-lg">{{ __('No students have taken this exam yet.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
