<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-comments text-indigo-600 mr-2"></i>
                    Forum Diskusi
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    {{ $stats['total_threads'] }} threads • {{ $stats['total_replies'] }} replies
                </p>
            </div>
            <a href="{{ route('forum.create') }}"
                class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-plus mr-2"></i>New Thread
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search Bar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('forum.search') }}" method="GET" class="flex gap-3">
                        <input type="text" name="q" placeholder="Search forum..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </form>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Categories</h3>

                    @if ($categories->count() > 0)
                        <div class="space-y-3">
                            @foreach ($categories as $category)
                                <a href="{{ route('forum.category', $category->slug) }}"
                                    class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center gap-4 flex-1">
                                            <div class="w-12 h-12 rounded-lg flex items-center justify-center"
                                                style="background-color: {{ $category->color }}20">
                                                <i class="{{ $category->icon }} text-2xl"
                                                    style="color: {{ $category->color }}"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-bold text-gray-900">{{ $category->name }}</h4>
                                                <p class="text-sm text-gray-600">{{ $category->description }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-2xl font-bold text-gray-900">
                                                {{ $category->threads_count }}</div>
                                            <div class="text-xs text-gray-500">threads</div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-inbox text-4xl mb-2"></i>
                            <p>No categories yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Latest Threads -->
            @if ($latestThreads->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-fire text-orange-500 mr-2"></i>
                            Latest Threads
                        </h3>
                        <div class="space-y-3">
                            @foreach ($latestThreads as $thread)
                                <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}"
                                    class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 mb-1">
                                                {!! $thread->status_badge !!}
                                                {{ $thread->title }}
                                            </h4>
                                            <div class="flex items-center gap-4 text-sm text-gray-600">
                                                <span>
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $thread->user->name }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-folder mr-1"></i>
                                                    {{ $thread->category->name }}
                                                </span>
                                                <span>
                                                    <i class="fas fa-comments mr-1"></i>
                                                    {{ $thread->replies_count }} replies
                                                </span>
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $thread->last_activity_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Popular Threads -->
            @if ($popularThreads->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">
                            <i class="fas fa-star text-yellow-500 mr-2"></i>
                            Popular Threads
                        </h3>
                        <div class="space-y-3">
                            @foreach ($popularThreads as $thread)
                                <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}"
                                    class="block p-4 border rounded-lg hover:bg-gray-50 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-bold text-gray-900 mb-1">{{ $thread->title }}</h4>
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
                                                    <i class="fas fa-eye mr-1"></i>
                                                    {{ $thread->views_count }} views
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

