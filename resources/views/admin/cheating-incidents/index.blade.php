<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-exclamation-triangle mr-2"></i>Insiden Kecurangan
            </h2>
            <a href="{{ route('admin.cheating-incidents.export', request()->query()) }}"
                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm">
                <i class="fas fa-file-csv"></i>Export CSV
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            {{-- Stats --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white shadow-md rounded-lg p-5 border-l-4 border-blue-600">
                    <p class="text-sm text-gray-600 font-semibold">Total Insiden</p>
                    <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-5 border-l-4 border-red-600">
                    <p class="text-sm text-gray-600 font-semibold">Sedang Diblokir</p>
                    <p class="mt-2 text-3xl font-bold text-red-600">{{ number_format($stats['blocked']) }}</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-5 border-l-4 border-green-600">
                    <p class="text-sm text-gray-600 font-semibold">Diselesaikan</p>
                    <p class="mt-2 text-3xl font-bold text-green-600">{{ number_format($stats['resolved']) }}</p>
                </div>
                <div class="bg-white shadow-md rounded-lg p-5 border-l-4 border-indigo-600">
                    <p class="text-sm text-gray-600 font-semibold">Pengguna Diblokir</p>
                    <p class="mt-2 text-3xl font-bold text-indigo-600">{{ number_format($stats['blocked_users']) }}</p>
                </div>
            </div>

            {{-- Incidents Table --}}
            {{-- Pass all resolvable IDs from PHP so toggleAll works without DOM querying --}}
            @php
                $resolvableIds = $incidents->where('status', '!=', 'resolved')->pluck('id')->values()->toJson();
            @endphp
            <div class="bg-white overflow-hidden shadow-md rounded-lg" x-data="bulkResolve({{ $resolvableIds }})">
                <div class="p-6">

                    {{-- Filter Form --}}
                    <form method="GET" action="{{ route('admin.cheating-incidents.index') }}"
                        class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-search text-gray-400 mr-1"></i>Cari
                            </label>
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                placeholder="Nama siswa / email / nama ujian">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-filter text-gray-400 mr-1"></i>Status
                            </label>
                            <select name="status"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <option value="">Semua</option>
                                <option value="blocked" @selected(request('status') === 'blocked')>Blocked</option>
                                <option value="reviewing" @selected(request('status') === 'reviewing')>Reviewing</option>
                                <option value="resolved" @selected(request('status') === 'resolved')>Resolved</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-filter"></i>Filter
                            </button>
                            <a href="{{ route('admin.cheating-incidents.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>Reset
                            </a>
                        </div>
                    </form>

                    {{-- Bulk Resolve Toolbar (visible when items selected) --}}
                    <div x-show="selected.length > 0" x-cloak
                        class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex flex-wrap items-center gap-4">
                        <span class="text-sm font-semibold text-yellow-800">
                            <span x-text="selected.length"></span> insiden dipilih
                        </span>
                        <button @click="openBulkModal = true"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all">
                            <i class="fas fa-check-double"></i>Selesaikan Semua
                        </button>
                        <button @click="clearSelection()" class="text-sm text-gray-500 hover:text-gray-700 underline">
                            Batalkan pilihan
                        </button>
                    </div>

                    {{-- Table --}}
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left">
                                        <input type="checkbox" @change="toggleAll($event)"
                                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Pengguna</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Ujian</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Alasan</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Terdeteksi</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($incidents as $incident)
                                    <tr class="hover:bg-gray-50 transition-colors"
                                        :class="selected.includes({{ $incident->id }}) ? 'bg-blue-50' : ''">
                                        <td class="px-4 py-4">
                                            @if ($incident->status !== 'resolved')
                                                <input type="checkbox" :value="{{ $incident->id }}" x-model="selected"
                                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <p class="text-sm font-semibold text-gray-900">
                                                {{ $incident->user->name ?? 'Guest' }}</p>
                                            <p class="text-xs text-gray-500">{{ $incident->user->email ?? '—' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900">{{ $incident->exam->title ?? '—' }}</p>
                                            <p class="text-xs text-gray-500">Attempt
                                                #{{ $incident->examAttempt?->id ?? '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-900">{{ $incident->reason ?? '—' }}</p>
                                            @if ($incident->details)
                                                <p class="text-xs text-gray-500">
                                                    Tab Switch: {{ $incident->details['tab_switches'] ?? '-' }},
                                                    Fullscreen: {{ $incident->details['fullscreen_exits'] ?? '-' }}
                                                </p>
                                            @endif
                                            @if ($incident->warning_count > 0)
                                                <span
                                                    class="inline-block mt-1 px-2 py-0.5 text-xs font-semibold rounded bg-orange-100 text-orange-700">
                                                    {{ $incident->warning_count }} peringatan sebelumnya
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">{!! $incident->status_badge !!}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ optional($incident->blocked_at)->format('d M Y H:i') ?? '—' }}
                                            @if ($incident->auto_unblock_at)
                                                <p class="text-xs text-blue-500 mt-1">
                                                    <i class="fas fa-clock mr-1"></i>Auto-unblock:
                                                    {{ $incident->auto_unblock_at->format('d M Y H:i') }}
                                                </p>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('admin.cheating-incidents.show', $incident) }}"
                                                class="text-blue-600 hover:text-blue-800 font-semibold text-sm">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i class="fas fa-shield-alt text-4xl text-gray-300 mb-3"></i>
                                                <p class="text-sm font-semibold">Tidak ada insiden.</p>
                                            </div>
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

                {{-- Bulk Resolve Modal --}}
                <div x-show="openBulkModal" x-cloak
                    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
                    @keydown.escape.window="openBulkModal = false">
                    <div class="bg-white rounded-xl shadow-xl w-full max-w-md mx-4 p-6" @click.stop>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-check-double text-green-600 mr-2"></i>
                            Selesaikan <span x-text="selected.length"></span> Insiden
                        </h3>

                        <form method="POST" action="{{ route('admin.cheating-incidents.bulk-resolve') }}">
                            @csrf
                            <template x-for="id in selected" :key="id">
                                <input type="hidden" name="incident_ids[]" :value="id">
                            </template>

                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Resolusi</label>
                                <textarea name="resolution_notes" rows="3"
                                    class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Opsional — catat tindakan yang dilakukan"></textarea>
                            </div>

                            <label class="flex items-center gap-2 text-sm text-gray-700 mb-6">
                                <input type="checkbox" name="reset_login" value="1"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                Aktifkan kembali akses login pengguna yang diblokir
                            </label>

                            <div class="flex justify-end gap-3">
                                <button type="button" @click="openBulkModal = false"
                                    class="px-4 py-2 text-sm font-semibold text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-all">
                                    Batal
                                </button>
                                <button type="submit"
                                    class="px-4 py-2 text-sm font-semibold text-white bg-green-600 rounded-lg hover:bg-green-700 transition-all">
                                    <i class="fas fa-check mr-1"></i>Konfirmasi
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Blocked Users --}}
            <div class="bg-white shadow-md rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-user-lock text-red-600 mr-2"></i>Pengguna yang sedang diblokir
                    </h3>
                    @if ($blockedUsers->isEmpty())
                        <p class="text-sm text-gray-500">Tidak ada pengguna yang diblokir.</p>
                    @else
                        <div class="overflow-x-auto border border-gray-200 rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Pengguna</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Diblokir Pada</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Insiden Aktif</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($blockedUsers as $blockedUser)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <p class="text-sm font-semibold text-gray-900">
                                                    {{ $blockedUser->name }}</p>
                                                <p class="text-xs text-gray-500">{{ $blockedUser->email }}</p>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ optional($blockedUser->login_blocked_at)->format('d M Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $blockedUser->active_cheating_incidents_count ?? 0 }}
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('admin.users.show', $blockedUser) }}"
                                                    class="text-blue-600 hover:text-blue-800 text-sm font-semibold">
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

    @push('scripts')
        <script>
            function bulkResolve(resolvableIds) {
                return {
                    resolvableIds: resolvableIds || [],
                    selected: [],
                    openBulkModal: false,
                    toggleAll(event) {
                        this.selected = event.target.checked ? [...this.resolvableIds] : [];
                    },
                    clearSelection() {
                        this.selected = [];
                        const headerCb = document.querySelector('thead input[type="checkbox"]');
                        if (headerCb) headerCb.checked = false;
                    },
                };
            }
        </script>
    @endpush
</x-app-layout>
