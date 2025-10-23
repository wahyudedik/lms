<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <a href="{{ route('forum.index') }}" class="text-gray-600 hover:text-gray-900">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                        style="background-color: {{ $category->color }}20">
                        <i class="{{ $category->icon }} text-xl" style="color: {{ $category->color }}"></i>
                    </div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ $category->name }}
                    </h2>
                </div>
                <p class="text-sm text-gray-600">{{ $category->description }}</p>
            </div>
            <a href="{{ route('forum.create-in-category', $category->slug) }}"
                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>New Thread
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search & Sort -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex gap-3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search in this category..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <select name="sort"
                            class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest Activity
                            </option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Most Replies
                            </option>
                        </select>
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Threads List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($threads->count() > 0)
                        <div class="space-y-4">
                            @foreach ($threads as $thread)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                    <div class="flex gap-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-white font-bold text-lg">
                                                {{ substr($thread->user->name, 0, 1) }}
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1">
                                            <a href="{{ route('forum.thread', [$category->slug, $thread->slug]) }}"
                                                class="block group">
                                                <h3
                                                    class="font-bold text-lg text-gray-900 group-hover:text-indigo-600 mb-2">
                                                    {!! $thread->status_badge !!}
                                                    {{ $thread->title }}
                                                </h3>
                                                <p class="text-sm text-gray-600 mb-3">{{ $thread->excerpt }}</p>
                                                <div class="flex items-center gap-4 text-sm text-gray-600">
                                                    <span>
                                                        <i class="fas fa-user mr-1"></i>
                                                        {{ $thread->user->name }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-comments mr-1"></i>
                                                        {{ $thread->replies_count }} replies
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-heart mr-1"></i>
                                                        {{ $thread->likes_count }} likes
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-eye mr-1"></i>
                                                        {{ $thread->views_count }} views
                                                    </span>
                                                </div>
                                            </a>
                                        </div>

                                        <!-- Last Activity -->
                                        @if ($thread->lastReplyUser)
                                            <div class="flex-shrink-0 text-right">
                                                <div class="text-xs text-gray-500">Last reply by</div>
                                                <div class="text-sm font-medium">{{ $thread->lastReplyUser->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $thread->last_activity_at->diffForHumans() }}</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $threads->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Threads Yet</h3>
                            <p class="text-gray-500 mb-6">Be the first to start a discussion!</p>
                            <a href="{{ route('forum.create-in-category', $category->slug) }}"
                                class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-plus mr-2"></i>Create First Thread
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

