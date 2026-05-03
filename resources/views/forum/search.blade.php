<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    <i class="fas fa-search text-purple-600 mr-2"></i>
                    Hasil Pencarian
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    Ditemukan {{ $threads->total() }} hasil untuk "{{ $search }}"
                </p>
            </div>
            <a href="{{ route('forum.index') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali ke Forum</span>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Search Again -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('forum.search') }}" method="GET" class="flex gap-3">
                        <input type="text" name="q" value="{{ $search }}" placeholder="Cari forum..."
                            class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500 focus:ring-opacity-50 transition-all duration-150">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-search"></i>
                            <span>Cari</span>
                        </button>
                        <a href="{{ route('forum.index') }}"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 shadow-sm">
                            <i class="fas fa-times"></i>
                            <span>Reset</span>
                        </a>
                    </form>
                </div>
            </div>

            <!-- Search Results -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg">
                <div class="p-6">
                    @if ($threads->count() > 0)
                        <div class="space-y-4">
                            @foreach ($threads as $thread)
                                <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                    <a href="{{ route('forum.thread', [$thread->category->slug, $thread->slug]) }}"
                                        class="block">
                                        <h3 class="font-bold text-lg text-gray-900 hover:text-purple-600 mb-2">
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
                                                {{ $thread->replies_count }} balasan
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
                            <div
                                class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                                <i class="fas fa-search-minus text-gray-400 text-3xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Tidak Ada Hasil</h3>
                            <p class="text-sm text-gray-500 mb-6">
                                Tidak ada thread yang cocok dengan pencarian "{{ $search }}". Coba kata kunci
                                lain.
                            </p>
                            <a href="{{ route('forum.index') }}"
                                class="inline-flex items-center gap-2 px-6 py-3 bg-purple-600 text-white font-semibold rounded-lg hover:bg-purple-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                <i class="fas fa-arrow-left"></i>
                                <span>Kembali ke Forum</span>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
