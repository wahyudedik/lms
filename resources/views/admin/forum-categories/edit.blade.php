<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit mr-2"></i>{{ __('Edit Category:') }} {{ $forumCategory->name }}
            </h2>
            <a href="{{ route('admin.forum-categories.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-600 text-white font-semibold rounded-lg hover:bg-gray-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-arrow-left"></i>
                {{ __('Back') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.forum-categories.update', $forumCategory) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Category Information -->
                        <div class="mb-8 pb-8 border-b border-gray-200">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-info-circle text-blue-600 mr-2"></i>{{ __('Category Information') }}
                            </h3>

                            <!-- Name -->
                            <div class="mb-6">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-tag text-gray-400 mr-1"></i>{{ __('Category Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" id="name"
                                    value="{{ old('name', $forumCategory->name) }}" required
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="{{ __('Enter category name...') }}">
                                @error('name')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-align-left text-gray-400 mr-1"></i>{{ __('Description') }}
                                </label>
                                <textarea name="description" id="description" rows="3"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150"
                                    placeholder="{{ __('Enter category description...') }}">{{ old('description', $forumCategory->description) }}</textarea>
                                @error('description')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Icon & Color Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Icon -->
                                <div>
                                    <label for="icon" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-icons text-gray-400 mr-1"></i>{{ __('Icon (FontAwesome)') }}
                                    </label>
                                    <input type="text" name="icon" id="icon"
                                        value="{{ old('icon', $forumCategory->icon) }}" placeholder="fas fa-comments"
                                        class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ __('Example: fas fa-book, fas fa-code, fas fa-graduation-cap') }}
                                    </p>
                                </div>

                                <!-- Color -->
                                <div>
                                    <label for="color" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-palette text-gray-400 mr-1"></i>{{ __('Color') }}
                                    </label>
                                    <input type="color" name="color" id="color"
                                        value="{{ old('color', $forumCategory->color) }}"
                                        class="h-10 w-20 rounded-lg border border-gray-300 shadow-sm">
                                </div>
                            </div>

                            <!-- Order -->
                            <div class="mb-6">
                                <label for="order" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-sort-numeric-down text-gray-400 mr-1"></i>{{ __('Order') }}
                                </label>
                                <input type="number" name="order" id="order"
                                    value="{{ old('order', $forumCategory->order) }}" min="0"
                                    class="block w-full px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50 transition-all duration-150">
                                <p class="text-sm text-gray-500 mt-1">{{ __('Lower numbers appear first') }}</p>
                            </div>

                            <!-- Active Status -->
                            <div class="mb-6">
                                <label class="flex items-center p-3 bg-green-50 rounded-lg border border-green-200">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $forumCategory->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-green-600 shadow-sm">
                                    <span class="ml-2 text-sm font-semibold text-gray-700">{{ __('Active') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Category Statistics -->
                        <div class="mb-8">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">
                                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>{{ __('Category Statistics') }}
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="p-3 bg-blue-50 rounded-lg border border-blue-100">
                                    <dt class="text-xs font-semibold text-blue-700 mb-1">{{ __('Total Threads') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $forumCategory->threads_count }}</dd>
                                </div>
                                <div class="p-3 bg-green-50 rounded-lg border border-green-100">
                                    <dt class="text-xs font-semibold text-green-700 mb-1">{{ __('Slug') }}</dt>
                                    <dd class="text-sm font-semibold text-gray-900">{{ $forumCategory->slug }}</dd>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.forum-categories.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                {{ __('Cancel') }}
                            </a>
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-save"></i>
                                {{ __('Update Category') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

