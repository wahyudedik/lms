<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-red-100 rounded-lg">
                    <i class="fas fa-terminal text-red-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                        {{ __('Log Error Aplikasi') }}
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5">
                        {{ __('Pantau log kesalahan, peringatan, dan pesan sistem laravel.log') }}
                    </p>
                </div>
            </div>
            <div class="flex gap-2 w-full sm:w-auto">
                <a href="{{ route('admin.error-logs.download') }}"
                    class="inline-flex flex-1 sm:flex-none justify-center items-center gap-2 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                    <i class="fas fa-download"></i>
                    {{ __('Unduh Log') }}
                </a>
                <form id="clear-logs-form" action="{{ route('admin.error-logs.clear') }}" method="POST"
                    class="inline flex-1 sm:flex-none">
                    @csrf
                    @method('DELETE')
                    <button type="button" id="clearLogsBtn"
                        class="inline-flex w-full justify-center items-center gap-2 px-4 py-2.5 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-trash-alt"></i>
                        {{ __('Hapus Log') }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Logs -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                    <div class="p-3 bg-blue-50 text-blue-600 rounded-lg">
                        <i class="fas fa-list text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ __('Total Log') }}
                        </p>
                        <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format($stats['total']) }}</p>
                    </div>
                </div>

                <!-- Errors -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                    <div class="p-3 bg-red-50 text-red-600 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('Error / Critical') }}</p>
                        <p class="text-2xl font-bold text-red-600 mt-1">{{ number_format($stats['error']) }}</p>
                    </div>
                </div>

                <!-- Warnings -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                    <div class="p-3 bg-amber-50 text-amber-600 rounded-lg">
                        <i class="fas fa-exclamation-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('Peringatan (Warning)') }}</p>
                        <p class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($stats['warning']) }}</p>
                    </div>
                </div>

                <!-- Info / Debug -->
                <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-200 flex items-center gap-4">
                    <div class="p-3 bg-emerald-50 text-emerald-600 rounded-lg">
                        <i class="fas fa-info-circle text-xl"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            {{ __('Info & Debug') }}</p>
                        <p class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($stats['info']) }}</p>
                    </div>
                </div>
            </div>

            <!-- Content Card -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg border border-gray-200">
                <div class="p-6 text-gray-900">

                    <!-- Search and Filter Form -->
                    <div class="mb-6">
                        <form method="GET" action="{{ route('admin.error-logs.index') }}"
                            class="flex flex-wrap gap-4 items-end">
                            <div class="flex-1 min-w-[280px]">
                                <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-search text-gray-400 mr-1"></i>{{ __('Pencarian') }}
                                </label>
                                <input type="text" id="search" name="search" value="{{ $search }}"
                                    placeholder="{{ __('Cari berdasarkan pesan log atau stack trace...') }}"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                            </div>

                            <div class="min-w-[180px]">
                                <label for="level" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i
                                        class="fas fa-filter text-gray-400 mr-1"></i>{{ __('Tingkat Keparahan (Level)') }}
                                </label>
                                <select id="level" name="level"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <option value="">{{ __('Semua Tingkat') }}</option>
                                    <option value="ERROR" {{ $level === 'ERROR' ? 'selected' : '' }}>
                                        {{ __('ERROR / CRITICAL') }}</option>
                                    <option value="WARNING" {{ $level === 'WARNING' ? 'selected' : '' }}>
                                        {{ __('WARNING') }}</option>
                                    <option value="INFO" {{ $level === 'INFO' ? 'selected' : '' }}>
                                        {{ __('INFO') }}</option>
                                    <option value="DEBUG" {{ $level === 'DEBUG' ? 'selected' : '' }}>
                                        {{ __('DEBUG') }}</option>
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                    <i class="fas fa-filter"></i>
                                    {{ __('Filter') }}
                                </button>
                                @if ($search || $level)
                                    <a href="{{ route('admin.error-logs.index') }}"
                                        class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                        <i class="fas fa-times"></i>
                                        {{ __('Bersihkan') }}
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Logs Table -->
                    <div class="overflow-x-auto border border-gray-200 rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 table-fixed">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="w-36 px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Tingkat (Level)') }}
                                    </th>
                                    <th
                                        class="w-48 px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Waktu (Timestamp)') }}
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Pesan') }}
                                    </th>
                                    <th
                                        class="w-24 px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                        {{ __('Detail') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" x-data="{ expanded: null, copied: false }">
                                @forelse($logs as $index => $log)
                                    @php
                                        $isError = in_array($log['level'], ['ERROR', 'CRITICAL', 'ALERT', 'EMERGENCY']);
                                        $isWarning = $log['level'] === 'WARNING';
                                        $isInfo = $log['level'] === 'INFO';
                                        $isDebug = $log['level'] === 'DEBUG';

                                        $badgeClass = match (true) {
                                            $isError => 'bg-red-100 text-red-800 border-red-200',
                                            $isWarning => 'bg-amber-100 text-amber-800 border-amber-200',
                                            $isInfo => 'bg-green-100 text-green-800 border-green-200',
                                            $isDebug => 'bg-blue-100 text-blue-800 border-blue-200',
                                            default => 'bg-gray-100 text-gray-800 border-gray-200',
                                        };
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors cursor-pointer group"
                                        @click="expanded = expanded === {{ $index }} ? null : {{ $index }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 border rounded-full text-xs font-bold {{ $badgeClass }}">
                                                <span
                                                    class="w-1.5 h-1.5 rounded-full mr-1.5
                                                    {{ $isError ? 'bg-red-500' : '' }}
                                                    {{ $isWarning ? 'bg-amber-500' : '' }}
                                                    {{ $isInfo ? 'bg-green-500' : '' }}
                                                    {{ $isDebug ? 'bg-blue-500' : '' }}
                                                "></span>
                                                {{ $log['level'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-mono">
                                            {{ $log['timestamp'] }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900 truncate font-mono">
                                            {{ $log['message'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                            <button type="button"
                                                class="text-blue-600 hover:text-blue-900 font-semibold inline-flex items-center gap-1">
                                                <span
                                                    x-text="expanded === {{ $index }} ? 'Tutup' : 'Detail'"></span>
                                                <i class="fas text-xs transition-transform duration-200"
                                                    :class="expanded === {{ $index }} ? 'fa-chevron-up' :
                                                        'fa-chevron-down'"></i>
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Collapsible Detail Section -->
                                    <tr x-show="expanded === {{ $index }}" x-cloak
                                        class="bg-gray-50 border-t border-b border-gray-200">
                                        <td colspan="4" class="px-8 py-6">
                                            <div class="space-y-4">
                                                <div class="flex justify-between items-center">
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-gray-800"><i
                                                                class="fas fa-align-left text-gray-400 mr-1.5"></i>Log
                                                            Message Detail</span>
                                                        <span
                                                            class="px-2 py-0.5 bg-gray-200 text-gray-700 text-xs font-mono rounded">Env:
                                                            {{ $log['env'] }}</span>
                                                    </div>
                                                    @if (!empty($log['stack']))
                                                        <button type="button"
                                                            @click.stop="navigator.clipboard.writeText($refs.stackBlock.innerText).then(() => {
                                                                copied = true;
                                                                setTimeout(() => copied = false, 2000);
                                                            })"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-300 text-gray-700 hover:text-blue-600 hover:border-blue-300 rounded-lg text-xs font-semibold shadow-sm transition-all duration-150">
                                                            <i class="fas"
                                                                :class="copied ? 'fa-check text-green-500' : 'fa-copy'"></i>
                                                            <span
                                                                x-text="copied ? 'Disalin!' : 'Salin Stack Trace'"></span>
                                                        </button>
                                                    @endif
                                                </div>

                                                <div
                                                    class="p-4 bg-gray-900 text-gray-100 rounded-lg font-mono text-xs overflow-x-auto whitespace-pre-wrap leading-relaxed shadow-inner">
                                                    <span class="text-red-400 font-bold">[{{ $log['timestamp'] }}]
                                                        {{ $log['env'] }}.{{ $log['level'] }}:</span>
                                                    {{ $log['message'] }}
                                                    @if (!empty($log['stack']))
                                                        <div x-ref="stackBlock"
                                                            class="mt-3 pt-3 border-t border-gray-800 text-gray-300 text-[11px] whitespace-pre font-mono overflow-x-auto">
                                                            {{ $log['stack'] }}</div>
                                                    @else
                                                        <div class="mt-2 text-gray-500 italic text-[11px]">No stack
                                                            trace associated with this entry.</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center text-gray-500">
                                                <i class="fas fa-clipboard-list text-5xl text-gray-300 mb-4"></i>
                                                <p class="text-base font-bold text-gray-700">
                                                    {{ __('Tidak Ada Log Ditemukan') }}</p>
                                                <p class="text-xs text-gray-400 mt-1">
                                                    {{ __('Belum ada riwayat log error atau kriteria filter tidak cocok.') }}
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if ($logs->hasPages())
                        <div class="mt-6">
                            {{ $logs->links() }}
                        </div>
                    @endif

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            // Confirm log clearing
            document.getElementById('clearLogsBtn').addEventListener('click', function(e) {
                e.preventDefault();
                Swal.fire({
                    title: '{{ __('Hapus Semua Log?') }}',
                    text: '{{ __('Tindakan ini akan mengosongkan seluruh file laravel.log secara permanen. Tindakan ini tidak dapat dibatalkan!') }}',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#374151',
                    confirmButtonText: '{{ __('Ya, Hapus Semua!') }}',
                    cancelButtonText: '{{ __('Batal') }}'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('clear-logs-form').submit();
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
