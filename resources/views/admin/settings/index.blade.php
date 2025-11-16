<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-cog text-blue-600 mr-2"></i>
                {{ __('Pengaturan Sistem') }}
            </h2>
            <a href="{{ route('admin.settings.backup') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                <i class="fas fa-database mr-2"></i>{{ __('Backup Database') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- General Settings -->
                @if (isset($settings['general']))
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Pengaturan Umum') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($settings['general'] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    @if ($setting->type === 'textarea')
                                        <textarea name="settings[{{ $setting->key }}]" rows="3"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $setting->value }}</textarea>
                                    @else
                                        <input type="text" name="settings[{{ $setting->key }}]"
                                            value="{{ $setting->value }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Localization Settings -->
                @if (isset($settings['localization']))
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-globe text-green-600 mr-2"></i>{{ __('Zona Waktu & Bahasa') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach ($settings['localization'] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ ucwords(str_replace('_', ' ', str_replace('app_', '', $setting->key))) }}
                                    </label>

                                    @if ($setting->key === 'app_timezone')
                                        <select name="settings[{{ $setting->key }}]"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            @foreach ($timezoneOptions as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ $setting->value === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @elseif($setting->key === 'app_locale')
                                        <select name="settings[{{ $setting->key }}]"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                            @foreach ($languageOptions as $value => $label)
                                                <option value="{{ $value }}"
                                                    {{ $setting->value === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-500 mt-4">
                            {{ __('Semua jadwal ujian disimpan dalam UTC dan otomatis ditampilkan mengikuti zona waktu aplikasi. Bahasa mempengaruhi teks bawaan sistem seperti validasi dan notifikasi.') }}
                        </p>
                    </div>
                @endif

                <!-- Appearance Settings -->
                @if (isset($settings['appearance']))
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-palette text-purple-600 mr-2"></i>{{ __('Tampilan') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            @foreach ($settings['appearance'] as $setting)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>

                                    @if ($setting->type === 'color')
                                        <input type="color" name="settings[{{ $setting->key }}]"
                                            value="{{ $setting->value }}"
                                            class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                    @elseif($setting->type === 'file')
                                        @if ($setting->value)
                                            <div class="mb-2">
                                                <img src="{{ Storage::url($setting->value) }}" alt="Current"
                                                    class="h-20 rounded-lg border">
                                            </div>
                                        @endif
                                        <input type="file" name="settings[{{ $setting->key }}]" accept="image/*"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Format: JPG, PNG (Max 2MB)') }}</p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- System Settings -->
                @if (isset($settings['system']))
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-server text-red-600 mr-2"></i>{{ __('Pengaturan Sistem') }}
                        </h2>
                        <div class="space-y-4">
                            @foreach ($settings['system'] as $setting)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div>
                                        <label class="text-sm font-medium text-gray-700">
                                            {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                        </label>
                                        @if ($setting->type === 'number')
                                            <p class="text-xs text-gray-500">{{ __('Ukuran dalam bytes (50MB = 52428800)') }}</p>
                                        @endif
                                    </div>

                                    @if ($setting->type === 'boolean')
                                        <label class="relative inline-flex items-center cursor-pointer">
                                            <input type="checkbox" name="settings[{{ $setting->key }}]" value="1"
                                                {{ $setting->value == '1' ? 'checked' : '' }} class="sr-only peer">
                                            <div
                                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                            </div>
                                        </label>
                                    @elseif($setting->type === 'number')
                                        <input type="number" name="settings[{{ $setting->key }}]"
                                            value="{{ $setting->value }}"
                                            class="w-32 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Notification Settings -->
                @if (isset($settings['notification']))
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-bell text-yellow-600 mr-2"></i>{{ __('Notifikasi') }}
                        </h2>
                        <div class="space-y-4">
                            @foreach ($settings['notification'] as $setting)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <label class="text-sm font-medium text-gray-700">
                                        {{ ucwords(str_replace('_', ' ', $setting->key)) }}
                                    </label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="settings[{{ $setting->key }}]" value="1"
                                            {{ $setting->value == '1' ? 'checked' : '' }} class="sr-only peer">
                                        <div
                                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Submit Button -->
                <div class="flex justify-end gap-4">
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition">
                        <i class="fas fa-times mr-2"></i>{{ __('Batal') }}
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-save mr-2"></i>{{ __('Simpan Pengaturan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
