<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>
                {{ __('Create New School') }}
            </h2>
            <a href="{{ route('admin.schools.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>{{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.schools.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Basic Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle mr-2"></i>{{ __('Basic Information') }}
                            </h3>

                            <!-- School Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('School Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="mb-4">
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Slug (URL-friendly)') }}
                                </label>
                                <input type="text" name="slug" id="slug" value="{{ old('slug') }}"
                                    placeholder="{{ __('Auto-generated if empty') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-sm text-gray-500 mt-1">{{ __('Leave empty to auto-generate from name') }}</p>
                                @error('slug')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email & Phone Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('Email') }}
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ __('Phone Number') }}
                                    </label>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Address') }}
                                </label>
                                <textarea name="address" id="address" rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Domain -->
                            <div class="mb-4">
                                <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Custom Domain (Optional)') }}
                                </label>
                                <input type="text" name="domain" id="domain" value="{{ old('domain') }}"
                                    placeholder="{{ __('school.yourdomain.com') }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-sm text-gray-500 mt-1">{{ __('For white-label multi-tenant setup') }}</p>
                                @error('domain')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Branding -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-palette mr-2"></i>{{ __('Branding') }}
                            </h3>

                            <!-- Logo -->
                            <div class="mb-4">
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Logo') }}
                                </label>
                                <input type="file" name="logo" id="logo"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full">
                                <p class="text-sm text-gray-500 mt-1">{{ __('Max 2MB. Formats: JPEG, PNG, JPG, GIF') }}</p>
                                @error('logo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Favicon -->
                            <div class="mb-4">
                                <label for="favicon" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Favicon') }}
                                </label>
                                <input type="file" name="favicon" id="favicon" accept="image/x-icon,image/png"
                                    class="w-full">
                                <p class="text-sm text-gray-500 mt-1">{{ __('Max 512KB. Formats: ICO, PNG') }}</p>
                                @error('favicon')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">{{ __('Active') }}</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t">
                            <a href="{{ route('admin.schools.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-save mr-2"></i>{{ __('Create School') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
