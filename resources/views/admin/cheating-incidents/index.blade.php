<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Cheating Incidents') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Total Incidents</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Currently Blocked</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($stats['blocked']) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Resolved</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($stats['resolved']) }}</p>
                </div>
                <div class="bg-white shadow-sm rounded-lg p-5">
                    <p class="text-sm text-gray-500">Users Blocked</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ number_format($stats['blocked_users']) }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.cheating-incidents.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Cari</label>
                            <input type="text" name="search" value="{{ request('search') }}" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Nama siswa / email / nama ujian">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Semua</option>
                                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                                <option value="reviewing" @selected(request('status') === 'reviewing')>Reviewing</option>
                                <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded">Filter</button>
                            <a href="{{ route('admin.cheating-incidents.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold px-4 py-2 rounded">Reset</a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Exam') }}</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Alasan</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terdeteksi</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($incidents as $incident)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $incident->user->name ?? 'Guest' }}</p>
                                                <p class="text-xs text-gray-500">{{ $incident->user->email ?? '—' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900">{{ $incident->exam->title ?? '—' }}</p>
                                            <p class="text-xs text-gray-500">Attempt #{{ $incident->examAttempt?->id ?? '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900">{{ $incident->reason ?? '—' }}</p>
                                            @if ($incident->details)
                                                <p class="text-xs text-gray-500">
                                                    Tab Switch: {{ $incident->details['tab_switches'] ?? '-' }},
                                                    Fullscreen: {{ $incident->details['fullscreen_exits'] ?? '-' }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{!! $incident->status_badge !!}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ optional($incident->blocked_at)->format('d M Y H:i') ?? '—' }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.cheating-incidents.show', $incident) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                            {{ __('No incidents.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $incidents->links() }}
                    </div>
                </div>
            </div>

            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Pengguna yang sedang diblokir</h3>
                    @if ($blockedUsers->isEmpty())
                        <p class="text-sm text-gray-500">{{ __('No blocked users.') }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diblokir Pada</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Insiden Aktif</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($blockedUsers as $blockedUser)
                                        <tr>
                                            <td class="px-6 py-4">
                                                <p class="text-sm font-medium text-gray-900">{{ $blockedUser->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $blockedUser->email }}</p>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ optional($blockedUser->login_blocked_at)->format('d M Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $blockedUser->active_cheating_incidents_count ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('admin.users.show', $blockedUser) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-semibold">
                                                    Lihat User
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

