<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-800">
                🎓 {{ __('Certificate Settings') }}
            </h2>
            <form action="{{ route('admin.certificate-settings.reset') }}" method="POST"
                onsubmit="return confirm('{{ __('Are you sure you want to reset to default settings?') }}')">
                @csrf
                <button type="submit" class="btn btn-outline">
                    <i class="fas fa-undo mr-2"></i>{{ __('Reset to Default') }}
                </button>
            </form>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>
                        <p class="text-green-800">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <form action="{{ route('admin.certificate-settings.update') }}" method="POST"
                enctype="multipart/form-data">
                @csrf

                <!-- Template Selection -->
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            <i class="fas fa-palette mr-2 text-indigo-600"></i>{{ __('Certificate Template') }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ __('Choose the design style for your certificates') }}</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                            @foreach ($templates as $key => $template)
                                <label class="cursor-pointer">
                                    <input type="radio" name="template" value="{{ $key }}"
                                        {{ $currentTemplate === $key ? 'checked' : '' }} class="peer sr-only" required>

                                    <div
                                        class="relative bg-white border-2 rounded-lg p-4 transition-all peer-checked:border-indigo-600 peer-checked:ring-2 peer-checked:ring-indigo-200 hover:border-gray-400">
                                        <!-- Selected Badge -->
                                        <div
                                            class="absolute -top-2 -right-2 bg-indigo-600 text-white px-3 py-1 rounded-full text-xs font-bold hidden peer-checked:block">
                                            {{ __('Selected') }}
                                        </div>

                                        <!-- Template Preview -->
                                        <div
                                            class="aspect-video bg-gradient-to-br 
                                            {{ $key === 'default' ? 'from-purple-100 to-pink-100' : '' }}
                                            {{ $key === 'modern' ? 'from-blue-100 to-purple-100' : '' }}
                                            {{ $key === 'elegant' ? 'from-amber-50 to-yellow-50' : '' }}
                                            {{ $key === 'minimalist' ? 'from-gray-100 to-gray-200' : '' }}
                                            rounded-lg mb-3 flex items-center justify-center border">
                                            <span class="text-4xl">
                                                @if ($key === 'default')
                                                    🏆
                                                @elseif($key === 'modern')
                                                    💎
                                                @elseif($key === 'elegant')
                                                    🎖️
                                                @else
                                                    ✨
                                                @endif
                                            </span>
                                        </div>

                                        <!-- Template Info -->
                                        <h4 class="font-bold text-gray-800 mb-1">{{ $template['name'] }}</h4>
                                        <p class="text-xs text-gray-600 mb-3">{{ $template['description'] }}</p>

                                        <!-- Features -->
                                        <div class="space-y-1 mb-3">
                                            @foreach ($template['features'] as $feature)
                                                <div class="flex items-center text-xs text-gray-600">
                                                    <i class="fas fa-check text-green-500 mr-2 text-[10px]"></i>
                                                    <span>{{ $feature }}</span>
                                                </div>
                                            @endforeach
                                        </div>

                                        <!-- Best For Badge -->
                                        <div class="pt-3 border-t">
                                            <span class="text-xs text-indigo-600 font-medium">
                                                👉 {{ $template['best_for'] }}
                                            </span>
                                        </div>

                                        <!-- Preview Button -->
                                        <button type="button" onclick="previewTemplate('{{ $key }}')"
                                            class="w-full mt-3 px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg text-xs font-medium transition-colors">
                                            <i class="fas fa-eye mr-1"></i>{{ __('Preview') }}
                                        </button>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Institution Information -->
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            <i class="fas fa-university mr-2 text-indigo-600"></i>{{ __('Institution Information') }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ __('Information displayed on certificates') }}</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Institution Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Institution Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="institution_name"
                                    value="{{ old('institution_name', $institutionName) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('institution_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Director Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Director Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="director_name"
                                    value="{{ old('director_name', $directorName) }}"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                    required>
                                @error('director_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Logo Upload -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Institution Logo') }}
                            </label>
                            <div class="flex items-center gap-4">
                                @if ($logoPath)
                                    <img src="{{ Storage::url($logoPath) }}" alt="Current Logo"
                                        class="w-20 h-20 object-contain border rounded-lg p-2">
                                @else
                                    <div
                                        class="w-20 h-20 bg-gray-100 border rounded-lg flex items-center justify-center">
                                        <i class="fas fa-image text-gray-400 text-2xl"></i>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg"
                                        class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                    <p class="mt-1 text-xs text-gray-500">{{ __('PNG or JPG. Max 2MB. Transparent background recommended.') }}</p>
                                </div>
                            </div>
                            @error('logo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Color Customization -->
                <div class="bg-white rounded-lg shadow-md mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-800 mb-2">
                            <i class="fas fa-fill-drip mr-2 text-indigo-600"></i>{{ __('Color Customization') }}
                        </h3>
                        <p class="text-sm text-gray-600">{{ __('Customize certificate colors (applies to Modern template)') }}</p>
                    </div>

                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Primary Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Primary Color') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="primary_color"
                                        value="{{ old('primary_color', $primaryColor) }}"
                                        class="w-20 h-12 rounded-lg border border-gray-300 cursor-pointer" required>
                                    <input type="text" value="{{ old('primary_color', $primaryColor) }}"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm"
                                        readonly>
                                </div>
                                @error('primary_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Secondary Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Secondary Color') }} <span class="text-red-500">*</span>
                                </label>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="secondary_color"
                                        value="{{ old('secondary_color', $secondaryColor) }}"
                                        class="w-20 h-12 rounded-lg border border-gray-300 cursor-pointer" required>
                                    <input type="text" value="{{ old('secondary_color', $secondaryColor) }}"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm"
                                        readonly>
                                </div>
                                @error('secondary_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Accent Color -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Accent Color') }} <span class="text-red-500">*</span>
                                <div class="flex items-center gap-3">
                                    <input type="color" name="accent_color"
                                        value="{{ old('accent_color', $accentColor) }}"
                                        class="w-20 h-12 rounded-lg border border-gray-300 cursor-pointer" required>
                                    <input type="text" value="{{ old('accent_color', $accentColor) }}"
                                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-mono text-sm"
                                        readonly>
                                </div>
                                @error('accent_color')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Color Presets -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                {{ __('Color Presets') }}
                            </label>
                            <div class="flex flex-wrap gap-3">
                                <button type="button" onclick="applyColorPreset('#3b82f6', '#8b5cf6', '#ec4899')"
                                    class="px-4 py-2 bg-gradient-to-r from-blue-500 to-pink-500 text-white rounded-lg text-sm font-medium hover:opacity-90">
                                    {{ __('Default Blue') }}
                                </button>
                                <button type="button" onclick="applyColorPreset('#059669', '#10b981', '#34d399')"
                                    class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-400 text-white rounded-lg text-sm font-medium hover:opacity-90">
                                    {{ __('Tech Green') }}
                                </button>
                                <button type="button" onclick="applyColorPreset('#7c3aed', '#8b5cf6', '#a78bfa')"
                                    class="px-4 py-2 bg-gradient-to-r from-purple-700 to-purple-400 text-white rounded-lg text-sm font-medium hover:opacity-90">
                                    {{ __('Royal Purple') }}
                                </button>
                                <button type="button" onclick="applyColorPreset('#dc2626', '#ef4444', '#f87171')"
                                    class="px-4 py-2 bg-gradient-to-r from-red-600 to-red-400 text-white rounded-lg text-sm font-medium hover:opacity-90">
                                    {{ __('Academic Red') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg font-medium transition-colors">
                        {{ __('Cancel') }}
                    </a>
                    <button type="submit"
                        class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>{{ __('Save Settings') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            // Preview template in new window
            function previewTemplate(template) {
                const url = '{{ route('admin.certificate-settings.preview') }}?template=' + template;
                window.open(url, 'Certificate Preview', 'width=1200,height=800');
            }

            // Apply color preset
            function applyColorPreset(primary, secondary, accent) {
                document.querySelector('input[name="primary_color"]').value = primary;
                document.querySelector('input[name="secondary_color"]').value = secondary;
                document.querySelector('input[name="accent_color"]').value = accent;

                // Update text inputs
                const primaryText = document.querySelector('input[name="primary_color"]').nextElementSibling;
                const secondaryText = document.querySelector('input[name="secondary_color"]').nextElementSibling;
                const accentText = document.querySelector('input[name="accent_color"]').nextElementSibling;

                primaryText.value = primary;
                secondaryText.value = secondary;
                accentText.value = accent;
            }

            // Sync color input with text
            document.querySelectorAll('input[type="color"]').forEach(input => {
                input.addEventListener('change', function() {
                    this.nextElementSibling.value = this.value;
                });
            });
        </script>
    @endpush
</x-app-layout>
