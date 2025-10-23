<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-edit text-yellow-600 mr-2"></i>
                Edit Category: {{ $forumCategory->name }}
            </h2>
            <a href="{{ route('admin.forum-categories.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('admin.forum-categories.update', $forumCategory) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-6">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                Category Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                value="{{ old('name', $forumCategory->name) }}" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-6">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                Description
                            </label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description', $forumCategory->description) }}</textarea>
                            @error('description')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Icon & Color Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Icon -->
                            <div>
                                <label for="icon" class="block text-sm font-medium text-gray-700 mb-2">
                                    Icon (FontAwesome)
                                </label>
                                <input type="text" name="icon" id="icon"
                                    value="{{ old('icon', $forumCategory->icon) }}" placeholder="fas fa-comments"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-sm text-gray-500 mt-1">
                                    Example: fas fa-book, fas fa-code, fas fa-graduation-cap
                                </p>
                            </div>

                            <!-- Color -->
                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                                    Color
                                </label>
                                <input type="color" name="color" id="color"
                                    value="{{ old('color', $forumCategory->color) }}"
                                    class="w-full h-10 rounded-md border-gray-300 shadow-sm">
                            </div>
                        </div>

                        <!-- Order & Status Row -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <!-- Order -->
                            <div>
                                <label for="order" class="block text-sm font-medium text-gray-700 mb-2">
                                    Order
                                </label>
                                <input type="number" name="order" id="order"
                                    value="{{ old('order', $forumCategory->order) }}" min="0"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <p class="text-sm text-gray-500 mt-1">Lower numbers appear first</p>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-end">
                                <label class="flex items-center">
                                    <input type="checkbox" name="is_active" value="1"
                                        {{ old('is_active', $forumCategory->is_active) ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-700">Active</span>
                                </label>
                            </div>
                        </div>

                        <!-- Stats Info -->
                        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-bold text-gray-900 mb-2">Category Statistics</h4>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Threads:</span>
                                    <span class="font-medium">{{ $forumCategory->threads_count }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Slug:</span>
                                    <span class="font-medium">{{ $forumCategory->slug }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('admin.forum-categories.index') }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-save mr-2"></i>Update Category
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

