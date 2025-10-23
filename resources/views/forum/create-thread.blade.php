<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <i class="fas fa-plus-circle text-indigo-600 mr-2"></i>
                {{ isset($thread) ? 'Edit Thread' : 'Create New Thread' }}
            </h2>
            <a href="{{ route('forum.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form
                        action="{{ isset($thread) ? route('forum.update', [$thread->category->slug, $thread->slug]) : route('forum.store') }}"
                        method="POST">
                        @csrf
                        @if (isset($thread))
                            @method('PUT')
                        @endif

                        <!-- Category -->
                        <div class="mb-6">
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Category <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" required
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select a category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $selectedCategory?->id ?? ($thread->category_id ?? '')) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Thread Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title"
                                value="{{ old('title', $thread->title ?? '') }}" required maxlength="255"
                                placeholder="Enter a descriptive title..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('title')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content" id="content" rows="10" required placeholder="Write your thread content..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $thread->content ?? '') }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">Minimum 10 characters</p>
                            @error('content')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tips -->
                        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <h4 class="font-bold text-blue-900 mb-2">
                                <i class="fas fa-lightbulb mr-2"></i>Tips for a Good Thread:
                            </h4>
                            <ul class="list-disc list-inside text-sm text-blue-800 space-y-1">
                                <li>Choose a clear and descriptive title</li>
                                <li>Provide enough context and details</li>
                                <li>Be respectful and constructive</li>
                                <li>Use proper formatting for readability</li>
                                <li>Select the appropriate category</li>
                            </ul>
                        </div>

                        <!-- Submit -->
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ url()->previous() }}"
                                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded">
                                Cancel
                            </a>
                            <button type="submit"
                                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                                <i class="fas fa-{{ isset($thread) ? 'save' : 'paper-plane' }} mr-2"></i>
                                {{ isset($thread) ? 'Update Thread' : 'Create Thread' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

