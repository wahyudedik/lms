<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Cheating Incident Detail') }}
                </h2>
                <p class="text-sm text-gray-500">Incident #{{ $incident->id }}</p>
            </div>
            <a href="{{ route('admin.cheating-incidents.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded font-semibold">{{ __('Back') }}</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <div class="bg-white shadow-sm rounded-lg">
                <div class="p-6">
                    <div class="flex flex-wrap justify-between gap-4">
                        <div>
                            <p class="text-sm uppercase text-gray-500">Status</p>
                            <div class="mt-1">{!! $incident->status_badge !!}</div>
                        </div>
                        <div>
                            <p class="text-sm uppercase text-gray-500">Terdeteksi</p>
                            <p class="text-lg font-semibold text-gray-900">
                                {{ optional($incident->blocked_at)->format('d M Y H:i') ?? $incident->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-sm uppercase text-gray-500">Alasan</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $incident->reason ?? __('None') }}</p>
                        </div>
                    </div>

                    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">Informasi Pengguna</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p class="font-semibold text-gray-900">{{ $incident->user->name ?? 'Guest' }}</p>
                                <p class="text-sm text-gray-500">{{ $incident->user->email ?? '—' }}</p>
                                @if ($incident->user)
                                    <p class="text-sm text-gray-500">Status Login:
                                        @if ($incident->user->is_login_blocked)
                                            <span class="text-red-600 font-semibold">Blocked</span>
                                        @else
                                            <span class="text-green-600 font-semibold">Allowed</span>
                                        @endif
                                    </p>
                                @endif
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-3">{{ __('Exam Information') }}</h3>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p class="font-semibold text-gray-900">{{ $incident->exam->title ?? '—' }}</p>
                                <p class="text-sm text-gray-500">Attempt ID: {{ $incident->examAttempt?->id ?? '—' }}</p>
                                <p class="text-sm text-gray-500">Kursus: {{ $incident->exam?->course?->title ?? '—' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-gray-900 mb-3">Detail Pelanggaran</h3>
                        <div class="bg-gray-50 rounded-lg p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <p class="text-sm text-gray-500">Tab Switches</p>
                                <p class="text-xl font-bold text-gray-900">{{ $incident->details['tab_switches'] ?? '0' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Fullscreen Exits</p>
                                <p class="text-xl font-bold text-gray-900">{{ $incident->details['fullscreen_exits'] ?? '0' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Tipe Deteksi</p>
                                <p class="text-xl font-bold text-gray-900">{{ $incident->details['type'] ?? $incident->type }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($incident->status !== 'resolved')
                        <div class="mt-10 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Tindak Lanjut</h3>
                            <form method="POST" action="{{ route('admin.cheating-incidents.resolve', $incident) }}" class="space-y-4">
                                @csrf
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan Resolusi</label>
                                    <textarea name="resolution_notes" rows="3" class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="Catat apa tindakan yang dilakukan"></textarea>
                                </div>

                                @if ($incident->user && $incident->user->is_login_blocked)
                                    <label class="flex items-center gap-2 text-sm text-gray-700">
                                        <input type="checkbox" name="reset_login" class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
                                        Aktifkan kembali akses login pengguna ini
                                    </label>
                                @endif

                                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-2 rounded">
                                    Tandai Selesai
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-10 border-t border-gray-200 pt-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Resolusi</h3>
                            <p class="text-sm text-gray-600">
                                Diselesaikan oleh {{ $incident->resolver?->name ?? 'System' }} pada
                                {{ optional($incident->resolved_at)->format('d M Y H:i') }}
                            </p>
                            @if ($incident->resolution_notes)
                                <p class="mt-2 text-gray-800">{{ $incident->resolution_notes }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

