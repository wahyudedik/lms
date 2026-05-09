<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-cog text-blue-600 mr-2"></i>
                {{ __('Pengaturan Sistem') }}
            </h2>
            <a href="{{ route('admin.settings.backup') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-database"></i>
                <span>{{ __('Backup Database') }}</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="settingsPage()" x-init="initTab()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 rounded-lg border border-blue-100 bg-blue-50 px-4 py-3 text-sm text-blue-800">
                {{ __('Gunakan tab di bawah untuk mengatur sistem, profil sekolah, tema, dan landing page.') }}
            </div>

            <!-- Tab Navigation -->
            <div class="mb-6 bg-white rounded-lg shadow-md p-2">
                <div class="flex flex-wrap gap-2">
                    <button type="button" @click="switchTab('general')"
                        :class="activeTab === 'general' ? 'bg-blue-600 text-white' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="flex-1 sm:flex-none px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-sm">
                        <i class="fas fa-cog mr-2"></i>
                        <span>{{ __('General') }}</span>
                    </button>
                    <button type="button" @click="switchTab('school')"
                        :class="activeTab === 'school' ? 'bg-blue-600 text-white' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="flex-1 sm:flex-none px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-sm">
                        <i class="fas fa-school mr-2"></i>
                        <span>{{ __('Profil Sekolah') }}</span>
                    </button>
                    <button type="button" @click="switchTab('theme')"
                        :class="activeTab === 'theme' ? 'bg-blue-600 text-white' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="flex-1 sm:flex-none px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-sm">
                        <i class="fas fa-palette mr-2"></i>
                        <span>{{ __('Tampilan') }}</span>
                    </button>
                    <button type="button" @click="switchTab('landing')"
                        :class="activeTab === 'landing' ? 'bg-blue-600 text-white' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="flex-1 sm:flex-none px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-sm">
                        <i class="fas fa-home mr-2"></i>
                        <span>{{ __('Landing Page') }}</span>
                    </button>
                    <button type="button" @click="switchTab('system')"
                        :class="activeTab === 'system' ? 'bg-blue-600 text-white' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="flex-1 sm:flex-none px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-sm">
                        <i class="fas fa-server mr-2"></i>
                        <span>{{ __('System') }}</span>
                    </button>
                    <button type="button" @click="switchTab('notifications')"
                        :class="activeTab === 'notifications' ? 'bg-blue-600 text-white' :
                            'bg-gray-200 text-gray-700 hover:bg-gray-300'"
                        class="flex-1 sm:flex-none px-6 py-3 rounded-lg font-semibold transition-all duration-200 shadow-sm">
                        <i class="fas fa-bell mr-2"></i>
                        <span>{{ __('Notifikasi') }}</span>
                    </button>
                </div>
            </div>

            <!-- General Tab Content -->
            <div x-show="activeTab === 'general'">
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
                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ __('Format: JPG, PNG (Max 2MB)') }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times"></i>
                            <span>{{ __('Batal') }}</span>
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-save"></i>
                            <span>{{ __('Simpan Pengaturan') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- School Profile Tab Content -->
            <div x-show="activeTab === 'school'">
                <form action="{{ route('admin.settings.school.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- School Identity Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-school text-blue-600 mr-2"></i>{{ __('Identitas Sekolah') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- School Name -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Nama Sekolah') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" value="{{ old('name', $school->name ?? '') }}"
                                    required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('name')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Email') }}
                                </label>
                                <input type="email" name="email"
                                    value="{{ old('email', $school->email ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('email')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Telepon') }}
                                </label>
                                <input type="text" name="phone"
                                    value="{{ old('phone', $school->phone ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('phone')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Alamat') }}
                                </label>
                                <textarea name="address" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('address', $school->address ?? '') }}</textarea>
                                @error('address')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Branding Assets Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-image text-purple-600 mr-2"></i>{{ __('Aset Branding') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Logo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Logo Sekolah') }}
                                </label>
                                @if ($school->logo ?? false)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $school->logo) }}" alt="Current Logo"
                                            class="h-24 rounded-lg border border-gray-200 shadow-sm">
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Logo saat ini') }}</p>
                                    </div>
                                @endif
                                <input type="file" name="logo"
                                    accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Format: JPG, PNG, GIF, SVG (Max 2MB)') }}
                                </p>
                                @error('logo')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Favicon -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Favicon') }}
                                </label>
                                @if ($school->favicon ?? false)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $school->favicon) }}" alt="Current Favicon"
                                            class="h-16 rounded border border-gray-200 shadow-sm">
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Favicon saat ini') }}</p>
                                    </div>
                                @endif
                                <input type="file" name="favicon" accept="image/x-icon,image/png,image/svg+xml"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Format: ICO, PNG, SVG (Max 512KB)') }}
                                </p>
                                @error('favicon')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times"></i>
                            <span>{{ __('Batal') }}</span>
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-save"></i>
                            <span>{{ __('Simpan Profil Sekolah') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Appearance (Theme) Tab Content -->
            <div x-show="activeTab === 'theme'">
                <form action="{{ route('admin.settings.theme.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- Theme Colors Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-palette text-purple-600 mr-2"></i>{{ __('Warna Tema') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Primary Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Utama') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="primary_color"
                                    value="{{ old('primary_color', $theme->primary_color ?? '#3B82F6') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('primary_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Secondary Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Sekunder') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="secondary_color"
                                    value="{{ old('secondary_color', $theme->secondary_color ?? '#10B981') }}"
                                    required class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('secondary_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Accent Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Aksen') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="accent_color"
                                    value="{{ old('accent_color', $theme->accent_color ?? '#8B5CF6') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('accent_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Success Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Sukses') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="success_color"
                                    value="{{ old('success_color', $theme->success_color ?? '#10B981') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('success_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Warning Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Peringatan') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="warning_color"
                                    value="{{ old('warning_color', $theme->warning_color ?? '#F59E0B') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('warning_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Danger Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Bahaya') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="danger_color"
                                    value="{{ old('danger_color', $theme->danger_color ?? '#EF4444') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('danger_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Info Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Info') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="info_color"
                                    value="{{ old('info_color', $theme->info_color ?? '#3B82F6') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('info_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Dark Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Warna Gelap') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="dark_color"
                                    value="{{ old('dark_color', $theme->dark_color ?? '#1F2937') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('dark_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Text Colors Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-font text-indigo-600 mr-2"></i>{{ __('Warna Teks') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Text Primary -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Teks Utama') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="text_primary"
                                    value="{{ old('text_primary', $theme->text_primary ?? '#1F2937') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('text_primary')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Text Secondary -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Teks Sekunder') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="text_secondary"
                                    value="{{ old('text_secondary', $theme->text_secondary ?? '#4B5563') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('text_secondary')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Text Muted -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Teks Redup') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="text_muted"
                                    value="{{ old('text_muted', $theme->text_muted ?? '#9CA3AF') }}" required
                                    class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('text_muted')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Background Colors Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-fill-drip text-teal-600 mr-2"></i>{{ __('Warna Latar Belakang') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            <!-- Background Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Latar Utama') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="background_color"
                                    value="{{ old('background_color', $theme->background_color ?? '#F9FAFB') }}"
                                    required class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('background_color')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Card Background -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Latar Kartu') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="card_background"
                                    value="{{ old('card_background', $theme->card_background ?? '#FFFFFF') }}"
                                    required class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('card_background')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Navbar Background -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Latar Navbar') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="navbar_background"
                                    value="{{ old('navbar_background', $theme->navbar_background ?? '#FFFFFF') }}"
                                    required class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('navbar_background')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Sidebar Background -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Latar Sidebar') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="color" name="sidebar_background"
                                    value="{{ old('sidebar_background', $theme->sidebar_background ?? '#1F2937') }}"
                                    required class="w-full h-12 px-2 border border-gray-300 rounded-lg cursor-pointer">
                                @error('sidebar_background')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Typography Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-text-height text-orange-600 mr-2"></i>{{ __('Tipografi') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Font Family -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Font Utama') }}
                                </label>
                                <input type="text" name="font_family"
                                    value="{{ old('font_family', $theme->font_family ?? 'Inter, sans-serif') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('font_family')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Heading Font -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Font Heading') }}
                                </label>
                                <input type="text" name="heading_font"
                                    value="{{ old('heading_font', $theme->heading_font ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('heading_font')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Font Size -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Ukuran Font (px)') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="font_size" min="10" max="24"
                                    value="{{ old('font_size', $theme->font_size ?? 16) }}" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('font_size')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Custom CSS Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-code text-pink-600 mr-2"></i>{{ __('CSS Kustom') }}
                        </h2>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Custom CSS') }}
                            </label>
                            <textarea name="custom_css" rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                                placeholder="/* Tambahkan CSS kustom di sini */">{{ old('custom_css', $theme->custom_css ?? '') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ __('CSS kustom akan diterapkan setelah tema default') }}
                            </p>
                            @error('custom_css')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Background Images Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-image text-cyan-600 mr-2"></i>{{ __('Gambar Latar Belakang') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Login Background -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Latar Login') }}
                                </label>
                                @if ($theme->login_background ?? false)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $theme->login_background) }}"
                                            alt="Current Login Background"
                                            class="h-32 w-full object-cover rounded-lg border border-gray-200 shadow-sm">
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Gambar saat ini') }}</p>
                                    </div>
                                @endif
                                <input type="file" name="login_background" accept="image/jpeg,image/png,image/jpg"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Format: JPG, PNG (Max 2MB)') }}
                                </p>
                                @error('login_background')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Dashboard Hero -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Hero Dashboard') }}
                                </label>
                                @if ($theme->dashboard_hero ?? false)
                                    <div class="mb-3">
                                        <img src="{{ asset('storage/' . $theme->dashboard_hero) }}"
                                            alt="Current Dashboard Hero"
                                            class="h-32 w-full object-cover rounded-lg border border-gray-200 shadow-sm">
                                        <p class="text-xs text-gray-500 mt-1">{{ __('Gambar saat ini') }}</p>
                                    </div>
                                @endif
                                <input type="file" name="dashboard_hero" accept="image/jpeg,image/png,image/jpg"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Format: JPG, PNG (Max 2MB)') }}
                                </p>
                                @error('dashboard_hero')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Effects Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-magic text-yellow-600 mr-2"></i>{{ __('Efek Visual') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Border Radius -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Border Radius') }}
                                </label>
                                <input type="text" name="border_radius"
                                    value="{{ old('border_radius', $theme->border_radius ?? '0.5rem') }}"
                                    placeholder="0.5rem"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('border_radius')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Box Shadow -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Box Shadow') }}
                                </label>
                                <input type="text" name="box_shadow"
                                    value="{{ old('box_shadow', $theme->box_shadow ?? '0 1px 3px rgba(0,0,0,0.1)') }}"
                                    placeholder="0 1px 3px rgba(0,0,0,0.1)"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('box_shadow')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Dark Mode -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Mode Gelap') }}
                                </label>
                                <label class="relative inline-flex items-center cursor-pointer mt-2">
                                    <input type="checkbox" name="dark_mode" value="1"
                                        {{ old('dark_mode', $theme->dark_mode ?? false) ? 'checked' : '' }}
                                        class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">{{ __('Aktifkan') }}</span>
                                </label>
                                @error('dark_mode')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Quick Palette Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-swatchbook text-green-600 mr-2"></i>{{ __('Palet Cepat') }}
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Klik salah satu palet di bawah untuk menerapkan skema warna yang telah ditentukan') }}
                        </p>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach ($palettes as $paletteName => $palette)
                                <form action="{{ route('admin.settings.theme.palette') }}" method="POST"
                                    class="inline">
                                    @csrf
                                    <input type="hidden" name="palette" value="{{ $paletteName }}">
                                    <button type="submit"
                                        class="w-full p-4 border-2 border-gray-200 rounded-lg hover:border-blue-500 transition-all duration-200 group">
                                        <div class="flex gap-2 mb-2">
                                            <div class="w-8 h-8 rounded"
                                                style="background-color: {{ $palette['primary_color'] }}"></div>
                                            <div class="w-8 h-8 rounded"
                                                style="background-color: {{ $palette['secondary_color'] }}"></div>
                                            <div class="w-8 h-8 rounded"
                                                style="background-color: {{ $palette['accent_color'] }}"></div>
                                        </div>
                                        <p class="text-sm font-medium text-gray-700 group-hover:text-blue-600">
                                            {{ ucfirst($paletteName) }}
                                        </p>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <!-- Submit and Reset Buttons -->
                    <div class="flex justify-between gap-4">
                        <form action="{{ route('admin.settings.theme.reset') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-sm hover:shadow-md"
                                onclick="return confirm('{{ __('Apakah Anda yakin ingin mereset tema ke default?') }}')">
                                <i class="fas fa-undo"></i>
                                <span>{{ __('Reset Tema') }}</span>
                            </button>
                        </form>

                        <div class="flex gap-4">
                            <a href="{{ route('admin.dashboard') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                <span>{{ __('Batal') }}</span>
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                <span>{{ __('Simpan Tema') }}</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Landing Page Tab Content -->
            <div x-show="activeTab === 'landing'" x-data="{
                features: {{ json_encode(old('features', $school->features ?? [])) }},
                statistics: {{ json_encode(old('statistics', $school->statistics ?? [])) }},
                addFeature() {
                    this.features.push({ icon: '', title: '', description: '' });
                },
                removeFeature(index) {
                    this.features.splice(index, 1);
                },
                addStatistic() {
                    this.statistics.push({ label: '', value: '' });
                },
                removeStatistic(index) {
                    this.statistics.splice(index, 1);
                }
            }">
                <form action="{{ route('admin.settings.landing.update') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <!-- General Settings Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-cog text-blue-600 mr-2"></i>{{ __('General Settings') }}
                        </h2>
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="show_landing_page" value="1"
                                    {{ old('show_landing_page', $school->show_landing_page ?? false) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <span
                                    class="ml-2 text-sm text-gray-600">{{ __('Enable landing page (uncheck to show default Laravel page)') }}</span>
                            </label>
                        </div>
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        {{ __('When enabled, visitors will see your custom landing page instead of the default Laravel welcome page. Make sure to fill in the sections below to create an engaging landing page.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hero Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-image text-purple-600 mr-2"></i>{{ __('Hero Section') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="hero_title"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Hero Title') }}</label>
                                <input type="text" name="hero_title" id="hero_title"
                                    value="{{ old('hero_title', $school->hero_title ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ __('Welcome to Our School') }}">
                                @error('hero_title')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="hero_subtitle"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Hero Subtitle') }}</label>
                                <input type="text" name="hero_subtitle" id="hero_subtitle"
                                    value="{{ old('hero_subtitle', $school->hero_subtitle ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ __('Learn, Grow, Succeed') }}">
                                @error('hero_subtitle')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mt-4">
                            <label for="hero_description"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Hero Description') }}</label>
                            <textarea name="hero_description" id="hero_description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('A brief description of your school and what makes it special...') }}">{{ old('hero_description', $school->hero_description ?? '') }}</textarea>
                            @error('hero_description')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mt-4">
                            <label for="hero_image"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Hero Background Image') }}</label>
                            @if ($school->hero_image ?? false)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $school->hero_image) }}"
                                        alt="{{ __('Current hero image') }}"
                                        class="h-32 rounded-lg border border-gray-200 shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Current hero image') }}</p>
                                </div>
                            @endif
                            <input type="file" name="hero_image" id="hero_image" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Recommended size: 1920x1080px (Max 2MB)') }}
                            </p>
                            @error('hero_image')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                            <div>
                                <label for="hero_cta_text"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Call-to-Action Button Text') }}</label>
                                <input type="text" name="hero_cta_text" id="hero_cta_text"
                                    value="{{ old('hero_cta_text', $school->hero_cta_text ?? __('Get Started')) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('hero_cta_text')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="hero_cta_link"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Call-to-Action Button Link') }}</label>
                                <input type="text" name="hero_cta_link" id="hero_cta_link"
                                    value="{{ old('hero_cta_link', $school->hero_cta_link ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="{{ __('/register or https://...') }}">
                                @error('hero_cta_link')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- About Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-info-circle text-green-600 mr-2"></i>{{ __('About Section') }}
                        </h2>
                        <div class="mb-4">
                            <label for="about_title"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('About Title') }}</label>
                            <input type="text" name="about_title" id="about_title"
                                value="{{ old('about_title', $school->about_title ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('About Our School') }}">
                            @error('about_title')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="about_content"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('About Content') }}</label>
                            <textarea name="about_content" id="about_content" rows="6"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('Tell visitors about your school, its mission, vision, and values...') }}">{{ old('about_content', $school->about_content ?? '') }}</textarea>
                            @error('about_content')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="about_image"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('About Image (Optional)') }}</label>
                            @if ($school->about_image ?? false)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $school->about_image) }}"
                                        alt="{{ __('Current about image') }}"
                                        class="h-32 rounded-lg border border-gray-200 shadow-sm">
                                    <p class="text-xs text-gray-500 mt-1">{{ __('Current about image') }}</p>
                                </div>
                            @endif
                            <input type="file" name="about_image" id="about_image" accept="image/*"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Recommended size: 600x400px (Max 2MB)') }}
                            </p>
                            @error('about_image')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Features Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-star text-yellow-600 mr-2"></i>{{ __('Features Section') }}
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Highlight the key features of your school (max 6 recommended)') }}</p>
                        <div class="space-y-4">
                            <template x-for="(feature, index) in features" :key="index">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium" x-text="`{{ __('Feature') }} #${index + 1}`"></h4>
                                        <button type="button" @click="removeFeature(index)"
                                            class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i> {{ __('Remove') }}
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Font Awesome Icon') }}</label>
                                            <input type="text" :name="`features[${index}][icon]`"
                                                x-model="feature.icon"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="fa-graduation-cap">
                                            <p class="text-xs text-gray-500 mt-1">{{ __('Search icons at') }} <a
                                                    href="https://fontawesome.com/icons" target="_blank"
                                                    class="text-blue-600">FontAwesome</a></p>
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Title') }}</label>
                                            <input type="text" :name="`features[${index}][title]`"
                                                x-model="feature.title"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                                            <input type="text" :name="`features[${index}][description]`"
                                                x-model="feature.description"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addFeature()"
                            class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-plus"></i>
                            <span>{{ __('Add Feature') }}</span>
                        </button>
                    </div>

                    <!-- Statistics Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-chart-bar text-indigo-600 mr-2"></i>{{ __('Statistics Section') }}
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Show impressive numbers about your school (max 4 recommended)') }}</p>
                        <div class="space-y-4">
                            <template x-for="(statistic, index) in statistics" :key="index">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium" x-text="`{{ __('Statistic') }} #${index + 1}`"></h4>
                                        <button type="button" @click="removeStatistic(index)"
                                            class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i> {{ __('Remove') }}
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Label') }}</label>
                                            <input type="text" :name="`statistics[${index}][label]`"
                                                x-model="statistic.label"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="{{ __('Active Students') }}">
                                        </div>
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Value') }}</label>
                                            <input type="text" :name="`statistics[${index}][value]`"
                                                x-model="statistic.value"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                placeholder="1000+">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <button type="button" @click="addStatistic()"
                            class="inline-flex items-center gap-2 mt-4 px-4 py-2.5 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-plus"></i>
                            <span>{{ __('Add Statistic') }}</span>
                        </button>
                    </div>

                    <!-- Contact Information Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-envelope text-red-600 mr-2"></i>{{ __('Contact Information') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="contact_email"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                                <input type="email" name="contact_email" id="contact_email"
                                    value="{{ old('contact_email', $school->contact_email ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_email')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="contact_phone"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Phone') }}</label>
                                <input type="text" name="contact_phone" id="contact_phone"
                                    value="{{ old('contact_phone', $school->contact_phone ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('contact_phone')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="contact_whatsapp"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('WhatsApp') }}</label>
                                <input type="text" name="contact_whatsapp" id="contact_whatsapp"
                                    value="{{ old('contact_whatsapp', $school->contact_whatsapp ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="+62xxx">
                                @error('contact_whatsapp')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label for="contact_address"
                                    class="block text-sm font-medium text-gray-700 mb-2">{{ __('Address') }}</label>
                                <textarea name="contact_address" id="contact_address" rows="3"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ old('contact_address', $school->contact_address ?? '') }}</textarea>
                                @error('contact_address')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-share-alt text-pink-600 mr-2"></i>{{ __('Social Media') }}
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="social_facebook" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-facebook text-blue-600 mr-2"></i>{{ __('Facebook URL') }}
                                </label>
                                <input type="url" name="social_facebook" id="social_facebook"
                                    value="{{ old('social_facebook', $school->social_facebook ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="https://facebook.com/yourschool">
                                @error('social_facebook')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="social_instagram" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-instagram text-pink-600 mr-2"></i>{{ __('Instagram URL') }}
                                </label>
                                <input type="url" name="social_instagram" id="social_instagram"
                                    value="{{ old('social_instagram', $school->social_instagram ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="https://instagram.com/yourschool">
                                @error('social_instagram')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="social_twitter" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-twitter text-blue-400 mr-2"></i>{{ __('Twitter URL') }}
                                </label>
                                <input type="url" name="social_twitter" id="social_twitter"
                                    value="{{ old('social_twitter', $school->social_twitter ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="https://twitter.com/yourschool">
                                @error('social_twitter')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                            <div>
                                <label for="social_youtube" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fab fa-youtube text-red-600 mr-2"></i>{{ __('YouTube URL') }}
                                </label>
                                <input type="url" name="social_youtube" id="social_youtube"
                                    value="{{ old('social_youtube', $school->social_youtube ?? '') }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="https://youtube.com/@yourschool">
                                @error('social_youtube')
                                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SEO Metadata Section -->
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                            <i class="fas fa-search text-teal-600 mr-2"></i>{{ __('SEO Settings') }}
                        </h2>
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Optimize your landing page for search engines') }}</p>
                        <div class="mb-4">
                            <label for="meta_title"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Meta Title') }}</label>
                            <input type="text" name="meta_title" id="meta_title"
                                value="{{ old('meta_title', $school->meta_title ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('Your School Name - Quality Education') }}">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Recommended: 50-60 characters') }}</p>
                            @error('meta_title')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="meta_description"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Meta Description') }}</label>
                            <textarea name="meta_description" id="meta_description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('A brief description of your school for search engines...') }}">{{ old('meta_description', $school->meta_description ?? '') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">{{ __('Recommended: 150-160 characters') }}</p>
                            @error('meta_description')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="meta_keywords"
                                class="block text-sm font-medium text-gray-700 mb-2">{{ __('Meta Keywords') }}</label>
                            <input type="text" name="meta_keywords" id="meta_keywords"
                                value="{{ old('meta_keywords', $school->meta_keywords ?? '') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="{{ __('education, online learning, school, courses') }}">
                            <p class="text-xs text-gray-500 mt-1">{{ __('Separate keywords with commas') }}</p>
                            @error('meta_keywords')
                                <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end gap-4">
                        <a href="{{ route('admin.dashboard') }}"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times"></i>
                            <span>{{ __('Batal') }}</span>
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-save"></i>
                            <span>{{ __('Simpan Landing Page') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- System Tab Content -->
            <div x-show="activeTab === 'system'">
                <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf

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
                                                <p class="text-xs text-gray-500">
                                                    {{ __('Ukuran dalam bytes (50MB = 52428800)') }}</p>
                                            @endif
                                        </div>

                                        @if ($setting->type === 'boolean')
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" name="settings[{{ $setting->key }}]"
                                                    value="1" {{ $setting->value == '1' ? 'checked' : '' }}
                                                    class="sr-only peer">
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
                                            <input type="checkbox" name="settings[{{ $setting->key }}]"
                                                value="1" {{ $setting->value == '1' ? 'checked' : '' }}
                                                class="sr-only peer">
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
                            class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times"></i>
                            <span>{{ __('Batal') }}</span>
                        </a>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-save"></i>
                            <span>{{ __('Simpan Pengaturan') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notifications Tab Content -->
            <div x-show="activeTab === 'notifications'" x-data="{
                pushEnabled: {{ \App\Models\Setting::get('push_notifications_enabled', '0') === '1' ? 'true' : 'false' }},
                togglingPush: false,
                async togglePush() {
                    this.togglingPush = true;
                    try {
                        const response = await fetch('{{ route('admin.settings.vapid.toggle-push') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        this.pushEnabled = data.enabled;
                    } catch (e) {
                        console.error('Toggle push failed:', e);
                    } finally {
                        this.togglingPush = false;
                    }
                }
            }">
                <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 border-b pb-2">
                        <i class="fas fa-bell text-yellow-600 mr-2"></i>{{ __('Push Notification (VAPID)') }}
                    </h2>

                    <!-- VAPID Status -->
                    <div class="mb-6">
                        <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('Status VAPID') }}</h3>
                        <div class="flex items-center gap-3">
                            @if (\App\Models\Setting::get('vapid_public_key'))
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    <i class="fas fa-check-circle"></i>
                                    {{ __('Terkonfigurasi') }}
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    <i class="fas fa-times-circle"></i>
                                    {{ __('Belum Dikonfigurasi') }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Generate VAPID Keys (Production) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="text-sm font-medium text-gray-700 mb-1">{{ __('Generate Otomatis (Production)') }}
                        </h3>
                        <p class="text-xs text-gray-500 mb-3">
                            {{ __('Generate VAPID keys secara otomatis. Membutuhkan OpenSSL dengan dukungan EC key (biasanya tersedia di server Linux/production).') }}
                        </p>
                        <form id="vapid-generate-form" action="{{ route('admin.settings.vapid.generate') }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="confirmed" value="1">
                            @if (\App\Models\Setting::get('vapid_public_key'))
                                <button type="button" onclick="confirmGenerateVapid()"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 text-white font-semibold rounded-lg hover:bg-yellow-700 transition-all duration-200 shadow-sm">
                                    <i class="fas fa-sync-alt"></i>
                                    <span>{{ __('Generate Ulang VAPID Keys') }}</span>
                                </button>
                            @else
                                <button type="submit"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm">
                                    <i class="fas fa-key"></i>
                                    <span>{{ __('Generate VAPID Keys') }}</span>
                                </button>
                            @endif
                        </form>
                    </div>

                    <!-- Input Manual VAPID Keys (Local/Windows) -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg" x-data="{ showManual: false }">
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="text-sm font-medium text-gray-700">{{ __('Input Manual (Local/Windows)') }}
                            </h3>
                            <button type="button" @click="showManual = !showManual"
                                class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                <span
                                    x-text="showManual ? '{{ __('Sembunyikan') }}' : '{{ __('Tampilkan') }}'"></span>
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mb-3">
                            {{ __('Jika generate otomatis gagal (Windows/Herd), gunakan opsi ini. Generate keys via terminal:') }}
                            <code class="bg-gray-200 px-1 rounded text-xs">php
                                vendor/minishlink/web-push/generateVAPIDKeys.php</code>
                            {{ __('atau gunakan') }}
                            <a href="https://vapidkeys.com" target="_blank"
                                class="text-blue-600 hover:underline">vapidkeys.com</a>
                        </p>
                        <form x-show="showManual" x-transition action="{{ route('admin.settings.vapid.manual') }}"
                            method="POST" class="space-y-3">
                            @csrf
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-600 mb-1">{{ __('Public Key (base64url)') }}</label>
                                <input type="text" name="vapid_public_key" placeholder="BEl62i..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ old('vapid_public_key') }}">
                                @error('vapid_public_key')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-medium text-gray-600 mb-1">{{ __('Private Key (base64url)') }}</label>
                                <input type="password" name="vapid_private_key" placeholder="..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm font-mono focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    value="{{ old('vapid_private_key') }}">
                                @error('vapid_private_key')
                                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white font-semibold rounded-lg hover:bg-green-700 transition-all duration-200 shadow-sm">
                                <i class="fas fa-save"></i>
                                <span>{{ __('Simpan VAPID Keys') }}</span>
                            </button>
                        </form>
                    </div>

                    <!-- Toggle Push Notification -->
                    <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-700">{{ __('Push Notification') }}</h3>
                                <p class="text-xs text-gray-500 mt-1">
                                    {{ __('Aktifkan atau nonaktifkan pengiriman push notification ke semua pengguna.') }}
                                </p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" class="sr-only peer" :checked="pushEnabled"
                                    :disabled="togglingPush" @change="togglePush()">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600">
                                </div>
                                <span class="ml-3 text-sm font-medium text-gray-700"
                                    x-text="pushEnabled ? '{{ __('Aktif') }}' : '{{ __('Nonaktif') }}'"></span>
                            </label>
                        </div>
                    </div>

                    <!-- VAPID Public Key Display -->
                    @if (\App\Models\Setting::get('vapid_public_key'))
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <h3 class="text-sm font-medium text-gray-700 mb-2">{{ __('VAPID Public Key') }}</h3>
                            <p class="text-xs text-gray-500 mb-2">
                                {{ __('Kunci publik ini digunakan oleh browser untuk mendaftarkan push subscription.') }}
                            </p>
                            <textarea readonly rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 text-sm font-mono text-gray-600 focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ \App\Models\Setting::get('vapid_public_key') }}</textarea>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function settingsPage() {
            return {
                activeTab: 'general',

                initTab() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const tabParam = urlParams.get('tab');
                    if (tabParam) {
                        this.activeTab = tabParam;
                    }
                },

                switchTab(tab) {
                    this.activeTab = tab;
                    const url = new URL(window.location);
                    url.searchParams.set('tab', tab);
                    window.history.pushState({}, '', url);
                }
            }
        }

        function confirmGenerateVapid() {
            Swal.fire({
                title: '{{ __('Generate Ulang VAPID Keys?') }}',
                text: '{{ __('Semua push subscription yang ada akan dihapus. Pengguna harus mendaftarkan ulang push notification mereka.') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '{{ __('Ya, Generate Ulang') }}',
                cancelButtonText: '{{ __('Batal') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('vapid-generate-form').submit();
                }
            });
        }
    </script>
</x-app-layout>
