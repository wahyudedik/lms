<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-palette text-purple-600 mr-2"></i>
                Theme Editor: {{ $school->name }}
            </h2>
            <a href="{{ route('admin.schools.show', $school) }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Quick Palettes -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">
                        <i class="fas fa-swatchbook mr-2"></i>Quick Apply Palettes
                    </h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                        @foreach ($palettes as $key => $palette)
                            <form action="{{ route('admin.schools.theme.apply-palette', $school) }}" method="POST"
                                class="text-center">
                                @csrf
                                <input type="hidden" name="palette" value="{{ $key }}">
                                <button type="submit"
                                    class="w-full p-4 border-2 rounded-lg hover:border-purple-500 transition group">
                                    <div class="flex gap-1 mb-2">
                                        <div class="flex-1 h-8 rounded"
                                            style="background-color: {{ $palette['primary_color'] }}"></div>
                                        <div class="flex-1 h-8 rounded"
                                            style="background-color: {{ $palette['secondary_color'] }}"></div>
                                        <div class="flex-1 h-8 rounded"
                                            style="background-color: {{ $palette['accent_color'] }}"></div>
                                    </div>
                                    <p class="text-sm font-medium group-hover:text-purple-600">
                                        {{ $palette['name'] }}
                                    </p>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Theme Form -->
            <form action="{{ route('admin.schools.theme.update', $school) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Main Colors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-fill-drip mr-2"></i>Main Colors
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Color</label>
                                <input type="color" name="primary_color"
                                    value="{{ old('primary_color', $theme->primary_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                                <input type="text" value="{{ old('primary_color', $theme->primary_color) }}"
                                    class="w-full mt-1 text-xs font-mono rounded border-gray-300" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Color</label>
                                <input type="color" name="secondary_color"
                                    value="{{ old('secondary_color', $theme->secondary_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                                <input type="text" value="{{ old('secondary_color', $theme->secondary_color) }}"
                                    class="w-full mt-1 text-xs font-mono rounded border-gray-300" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Accent Color</label>
                                <input type="color" name="accent_color"
                                    value="{{ old('accent_color', $theme->accent_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                                <input type="text" value="{{ old('accent_color', $theme->accent_color) }}"
                                    class="w-full mt-1 text-xs font-mono rounded border-gray-300" readonly>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dark Color</label>
                                <input type="color" name="dark_color"
                                    value="{{ old('dark_color', $theme->dark_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                                <input type="text" value="{{ old('dark_color', $theme->dark_color) }}"
                                    class="w-full mt-1 text-xs font-mono rounded border-gray-300" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status Colors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-traffic-light mr-2"></i>Status Colors
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Success</label>
                                <input type="color" name="success_color"
                                    value="{{ old('success_color', $theme->success_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Warning</label>
                                <input type="color" name="warning_color"
                                    value="{{ old('warning_color', $theme->warning_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Danger</label>
                                <input type="color" name="danger_color"
                                    value="{{ old('danger_color', $theme->danger_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Info</label>
                                <input type="color" name="info_color"
                                    value="{{ old('info_color', $theme->info_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Text Colors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-font mr-2"></i>Text Colors
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Text</label>
                                <input type="color" name="text_primary"
                                    value="{{ old('text_primary', $theme->text_primary) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Secondary Text</label>
                                <input type="color" name="text_secondary"
                                    value="{{ old('text_secondary', $theme->text_secondary) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Muted Text</label>
                                <input type="color" name="text_muted"
                                    value="{{ old('text_muted', $theme->text_muted) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Background Colors -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-layer-group mr-2"></i>Background Colors
                        </h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Page Background</label>
                                <input type="color" name="background_color"
                                    value="{{ old('background_color', $theme->background_color) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Card Background</label>
                                <input type="color" name="card_background"
                                    value="{{ old('card_background', $theme->card_background) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Navbar Background</label>
                                <input type="color" name="navbar_background"
                                    value="{{ old('navbar_background', $theme->navbar_background) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Sidebar Background</label>
                                <input type="color" name="sidebar_background"
                                    value="{{ old('sidebar_background', $theme->sidebar_background) }}"
                                    class="w-full h-12 rounded border-gray-300">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-text-height mr-2"></i>Typography
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Font Family</label>
                                <input type="text" name="font_family"
                                    value="{{ old('font_family', $theme->font_family) }}"
                                    placeholder="Inter, sans-serif" class="w-full rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Heading Font
                                    (Optional)</label>
                                <input type="text" name="heading_font"
                                    value="{{ old('heading_font', $theme->heading_font) }}"
                                    placeholder="Same as body font" class="w-full rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Base Font Size (px)</label>
                                <input type="number" name="font_size"
                                    value="{{ old('font_size', $theme->font_size) }}" min="10" max="24"
                                    class="w-full rounded border-gray-300">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-cog mr-2"></i>Advanced Settings
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Border Radius</label>
                                <input type="text" name="border_radius"
                                    value="{{ old('border_radius', $theme->border_radius) }}" placeholder="0.5rem"
                                    class="w-full rounded border-gray-300">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Box Shadow</label>
                                <input type="text" name="box_shadow"
                                    value="{{ old('box_shadow', $theme->box_shadow) }}"
                                    placeholder="0 1px 3px 0 rgb(0 0 0 / 0.1)" class="w-full rounded border-gray-300">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="dark_mode" value="1"
                                    {{ old('dark_mode', $theme->dark_mode) ? 'checked' : '' }}
                                    class="rounded border-gray-300">
                                <span class="ml-2 text-sm text-gray-700">Enable Dark Mode</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Custom CSS -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-code mr-2"></i>Custom CSS
                        </h3>
                        <textarea name="custom_css" rows="8" placeholder="/* Add your custom CSS here */"
                            class="w-full font-mono text-sm rounded border-gray-300">{{ old('custom_css', $theme->custom_css) }}</textarea>
                        <p class="text-sm text-gray-500 mt-2">Advanced: Add custom CSS rules to override defaults</p>
                    </div>
                </div>

                <!-- Background Images -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">
                            <i class="fas fa-image mr-2"></i>Background Images
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Login Background</label>
                                @if ($theme->login_background)
                                    <div class="mb-2">
                                        <img src="{{ $theme->login_background_url }}" alt="Login BG"
                                            class="h-32 w-full object-cover rounded">
                                    </div>
                                @endif
                                <input type="file" name="login_background" accept="image/jpeg,image/png,image/jpg"
                                    class="w-full">
                                <p class="text-sm text-gray-500 mt-1">Max 2MB</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Dashboard Hero</label>
                                @if ($theme->dashboard_hero)
                                    <div class="mb-2">
                                        <img src="{{ $theme->dashboard_hero_url }}" alt="Dashboard Hero"
                                            class="h-32 w-full object-cover rounded">
                                    </div>
                                @endif
                                <input type="file" name="dashboard_hero" accept="image/jpeg,image/png,image/jpg"
                                    class="w-full">
                                <p class="text-sm text-gray-500 mt-1">Max 2MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div class="flex gap-2">
                                <form action="{{ route('admin.schools.theme.reset', $school) }}" method="POST"
                                    onsubmit="return confirmDelete('This will reset all theme settings to default. Are you sure?')">
                                    @csrf
                                    <button type="submit"
                                        class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                        <i class="fas fa-undo mr-2"></i>Reset to Default
                                    </button>
                                </form>

                                <a href="{{ route('admin.schools.theme.export', $school) }}"
                                    class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fas fa-download mr-2"></i>Export JSON
                                </a>
                            </div>

                            <button type="submit"
                                class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-save mr-2"></i>Save Theme
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
