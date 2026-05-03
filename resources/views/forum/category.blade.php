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
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                <i class="fas fa-plus"></i>
                <span>Thread Baru</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search & Sort -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" class="flex gap-3">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari di kategori ini..."
                            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-150">
                        <select name="sort"
                            class="px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-150"
                            onchange="this.form.submit()">
                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Aktivitas Terbaru
                            </option>
                            <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Paling Banyak
                                Balasan
                            </option>
                        </select>
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-search"></i>
                            <span>Cari</span>
                        </button>
                        @if (request('search') || request('sort'))
                            <a href="{{ route('forum.category', $category->slug) }}"
                                class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                                <i class="fas fa-times"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Threads List -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    @if ($threads->count() > 0)
                        <div class="space-y-4">
                            @foreach ($threads as $thread)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex gap-4">
                                        <!-- Avatar -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600 font-bold text-lg">
                                                {{ substr($thread->user->name, 0, 1) }}
                                            </div>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-1">
                                            <a href="{{ route('forum.thread', [$category->slug, $thread->slug]) }}"
                                                class="block group">
                                                <h3
                                                    class="font-bold text-lg text-gray-900 group-hover:text-purple-600 mb-2">
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
                                                        {{ $thread->replies_count }} balasan
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-heart mr-1"></i>
                                                        {{ $thread->likes_count }} suka
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-eye mr-1"></i>
                                                        {{ $thread->views_count }} dilihat
                                                    </span>
                                                </div>
                                            </a>
                                        </div>

                                        <!-- Last Activity -->
                                        @if ($thread->lastReplyUser)
                                            <div class="flex-shrink-0 text-right">
                                                <div class="text-xs text-gray-500">Balasan terakhir oleh</div>
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
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-inbox text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Thread</h3>
                            <p class="text-sm text-gray-500 mb-6">Jadilah yang pertama memulai diskusi!</p>
                            <a href="{{ route('forum.create-in-category', $category->slug) }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-plus"></i>
                                <span>Buat Thread Pertama</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
