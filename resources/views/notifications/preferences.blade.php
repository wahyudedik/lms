<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-100 rounded-lg">
                    <i class="fas fa-sliders-h text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Preferensi Notifikasi</h2>
                    <p class="text-sm text-gray-600">Atur jenis notifikasi yang ingin Anda terima</p>
                </div>
            </div>
            <a href="{{ route('notifications.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-600"></i>
                        <span class="text-sm text-green-700 font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-lg shadow-md border border-gray-200">
                <form action="{{ route('notifications.preferences.update') }}" method="POST">
                    @csrf

                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-gray-50 border-b border-gray-200">
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Jenis Notifikasi
                                    </th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Database</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-700">Push</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($types as $type => $label)
                                    @php
                                        $pref = $preferences[$type] ?? null;
                                        $viaDatabase = $pref ? $pref->via_database : true;
                                        $viaPush = $pref ? $pref->via_push : true;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors" x-data="{ viaDatabase: {{ $viaDatabase ? 'true' : 'false' }}, viaPush: {{ $viaPush ? 'true' : 'false' }} }">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="hidden" name="preferences[{{ $type }}][via_database]"
                                                value="0">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox"
                                                    name="preferences[{{ $type }}][via_database]" value="1"
                                                    x-model="viaDatabase" class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                                </div>
                                            </label>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <input type="hidden" name="preferences[{{ $type }}][via_push]"
                                                value="0">
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="preferences[{{ $type }}][via_push]"
                                                    value="1" x-model="viaPush" class="sr-only peer">
                                                <div
                                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                                </div>
                                            </label>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 rounded-b-lg">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white rounded-lg shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors font-medium">
                            <i class="fas fa-save"></i>
                            <span>Simpan Preferensi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
