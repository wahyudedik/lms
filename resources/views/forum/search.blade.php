<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-search text-indigo-600 mr-2"></i>
                    Search Results
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Found {{ $threads->total() }} result(s) for "{{ $search }}"
                </p>
            </div>
            <a href="{{ route('forum.index') }}"
                class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                <i class="fas fa-arrow-left mr-2"></i>Back to Forum
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search Again -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('forum.search') }}" method="GET" class="flex gap-3">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Search forum..."
                            class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <button type="submit"
                            class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                        <a href="{{ route('forum.index') }}"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded flex items-center">
                            <i class="fas fa-times mr-2"></i>Reset
                        </a>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if ($threads->count() > 0)
                        <div class="space-y-4">
                            @foreach ($threads as $thread)
                                <div class="border rounded-lg p-4 hover:bg-gray-50 transition">
                                    <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}"
                                        class="block">
                                        <h3 class="font-bold text-lg text-gray-900 hover:text-indigo-600 mb-2">
                                            {!! $thread->status_badge !!}
                                            {{ $thread->title }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mb-3">{{ $thread->excerpt }}</p>
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span>
                                                <i class="fas fa-folder mr-1"></i>
                                                {{ $thread->category->name }}
                                            </span>
                                            <span>
                                                <i class="fas fa-user mr-1"></i>
                                                {{ $thread->user->name }}
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
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $threads->appends(['q' => $search])->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="fas fa-search-minus text-6xl text-gray-300 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Results Found</h3>
                            <p class="text-gray-500 mb-6">
                                No threads match your search for "{{ $search }}". Try different keywords.
                            </p>
                            <a href="{{ route('forum.index') }}"
                                class="inline-block bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                                <i class="fas fa-arrow-left mr-2"></i>Back to Forum
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
