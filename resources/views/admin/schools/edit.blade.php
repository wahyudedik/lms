<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit School: {{ $school->name }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('admin.schools.theme.edit', $school) }}"
                    class="bg-purple-500 hover:bg-purple-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-palette mr-2"></i>Theme
                </a>
                <a href="{{ route('admin.schools.show', $school) }}"
                    class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.schools.update', $school) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle mr-2"></i>Basic Information
                            </h3>

                            <!-- School Name -->
                            <div class="mb-4">
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    School Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $school->name) }}" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Slug -->
                            <div class="mb-4">
                                <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                                    Slug (URL-friendly)
                                </label>
                                <input type="text" name="slug" id="slug"
                                    value="{{ old('slug', $school->slug) }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('slug')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email & Phone Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                        Email
                                    </label>
                                    <input type="email" name="email" id="email"
                                        value="{{ old('email', $school->email) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('email')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                        Phone
                                    </label>
                                    <input type="text" name="phone" id="phone"
                                        value="{{ old('phone', $school->phone) }}"
                                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('phone')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Address -->
                            <div class="mb-4">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Address
                                </label>
                                <textarea name="address" id="address" rows="3"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address', $school->address) }}</textarea>
                                @error('address')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Domain -->
                            <div class="mb-4">
                                <label for="domain" class="block text-sm font-medium text-gray-700 mb-2">
                                    Custom Domain (Optional)
                                </label>
                                <input type="text" name="domain" id="domain"
                                    value="{{ old('domain', $school->domain) }}" placeholder="school.yourdomain.com"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-sm text-gray-500 mt-1">For white-label multi-tenant setup</p>
                                @error('domain')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Current Branding -->
                        @if ($school->logo || $school->favicon)
                            <div class="mb-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">
                                    <i class="fas fa-image mr-2"></i>Current Branding
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    @if ($school->logo)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Logo:</p>
                                            <img src="{{ $school->logo_url }}" alt="Logo"
                                                class="h-20 object-contain border rounded p-2">
                                        </div>
                                    @endif
                                    @if ($school->favicon)
                                        <div>
                                            <p class="text-sm font-medium text-gray-700 mb-2">Favicon:</p>
                                            <img src="{{ $school->favicon_url }}" alt="Favicon"
                                                class="h-10 object-contain border rounded p-2">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Update Branding -->
                        <div class="mb-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-palette mr-2"></i>Update Branding
                            </h3>

                            <!-- Logo -->
                            <div class="mb-4">
                                <label for="logo" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Logo (Optional)
                                </label>
                                <input type="file" name="logo" id="logo"
                                    accept="image/jpeg,image/png,image/jpg,image/gif" class="w-full">
                                <p class="text-sm text-gray-500 mt-1">Max 2MB. Leave empty to keep current</p>
                                @error('logo')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Favicon -->
                            <div class="mb-4">
                                <label for="favicon" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Favicon (Optional)
                                </label>
                                <input type="file" name="favicon" id="favicon" accept="image/x-icon,image/png"
                                    class="w-full">
                                <p class="text-sm text-gray-500 mt-1">Max 512KB. Leave empty to keep current</p>
                                @error('favicon')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $school->is_active) ? 'checked' : '' }}
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-3 pt-6 border-t">
                            <a href="{{ route('admin.schools.show', $school) }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-save mr-2"></i>Update School
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
