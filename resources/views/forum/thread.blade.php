<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex-1">
                <div class="flex items-center gap-2 text-sm text-gray-600 mb-2">
                    <a href="{{ route('forum.index') }}" class="hover:text-gray-900">Forum</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="{{ route('forum.category', $thread->category->slug) }}" class="hover:text-gray-900">
                        {{ $thread->category->name }}
                    </a>
                </div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {!! $thread->status_badge !!}
                    {{ $thread->title }}
                </h2>
            </div>
            <div class="flex gap-2">
                @if (auth()->id() === $thread->user_id || auth()->user()->isAdmin())
                    <a href="{{ route('forum.edit', [$thread->category->slug, $thread->slug]) }}"
                        class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-edit"></i>
                    </a>
                @endif
                @if (auth()->user()->isAdmin() || auth()->user()->isGuru())
                    <form action="{{ route('forum.pin', [$thread->category->slug, $thread->slug]) }}" method="POST"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-{{ $thread->is_pinned ? 'gray' : 'purple' }}-500 hover:bg-{{ $thread->is_pinned ? 'gray' : 'purple' }}-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-thumbtack"></i>
                        </button>
                    </form>
                    <form action="{{ route('forum.lock', [$thread->category->slug, $thread->slug]) }}" method="POST"
                        class="inline">
                        @csrf
                        <button type="submit"
                            class="bg-{{ $thread->is_locked ? 'gray' : 'red' }}-500 hover:bg-{{ $thread->is_locked ? 'gray' : 'red' }}-700 text-white font-bold py-2 px-4 rounded">
                            <i class="fas fa-lock"></i>
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Thread Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <!-- Author Info -->
                    <div class="flex gap-4 mb-6">
                        <div
                            class="w-16 h-16 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-white font-bold text-2xl">
                            {{ substr($thread->user->name, 0, 1) }}
                        </div>
                        <div>
                            <div class="font-bold text-lg">{{ $thread->user->name }}</div>
                            <div class="text-sm text-gray-600">
                                {{ $thread->created_at->format('M d, Y H:i') }}
                                <span class="mx-2">•</span>
                                <i class="fas fa-eye mr-1"></i>{{ $thread->views_count }} views
                            </div>
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="prose max-w-none mb-6">
                        {!! nl2br(e($thread->content)) !!}
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-4 pt-4 border-t">
                        <button onclick="toggleLike('thread', {{ $thread->id }})"
                            id="like-thread-{{ $thread->id }}"
                            class="flex items-center gap-2 px-4 py-2 rounded {{ $thread->isLikedBy(auth()->user()) ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }} hover:bg-red-100 hover:text-red-600 transition">
                            <i class="fas fa-heart"></i>
                            <span id="likes-count-thread-{{ $thread->id }}">{{ $thread->likes_count }}</span>
                        </button>
                        <a href="#reply-form"
                            class="flex items-center gap-2 px-4 py-2 rounded bg-gray-100 text-gray-600 hover:bg-indigo-100 hover:text-indigo-600 transition">
                            <i class="fas fa-reply"></i>
                            Reply
                        </a>
                    </div>
                </div>
            </div>

            <!-- Replies -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">
                        <i class="fas fa-comments mr-2"></i>
                        {{ $thread->replies_count }} Replies
                    </h3>

                    @if ($replies->count() > 0)
                        <div class="space-y-4">
                            @foreach ($replies as $reply)
                                @include('forum.partials.reply', ['reply' => $reply, 'thread' => $thread])
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $replies->links() }}
                        </div>
                    @else
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-comment-slash text-4xl mb-2"></i>
                            <p>No replies yet. Be the first to respond!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reply Form -->
            @if (!$thread->is_locked || auth()->user()->isAdmin())
                <div id="reply-form" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Post a Reply</h3>
                        <form action="{{ route('forum.reply', [$thread->category->slug, $thread->slug]) }}"
                            method="POST">
                            @csrf
                            <input type="hidden" name="parent_id" id="parent_id" value="">
                            <div class="mb-4" id="reply-to-notice" style="display: none;">
                                <div
                                    class="bg-blue-50 border border-blue-200 rounded p-3 flex items-center justify-between">
                                    <span class="text-blue-800 text-sm">
                                        <i class="fas fa-reply mr-2"></i>
                                        Replying to <strong id="reply-to-name"></strong>
                                    </span>
                                    <button type="button" onclick="cancelReply()"
                                        class="text-blue-600 hover:text-blue-800">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <textarea name="content" id="reply-content" rows="4" required placeholder="Write your reply..."
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            <div class="flex justify-end mt-4">
                                <button type="submit"
                                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded">
                                    <i class="fas fa-paper-plane mr-2"></i>Post Reply
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                    <i class="fas fa-lock text-4xl text-yellow-600 mb-2"></i>
                    <p class="text-yellow-800 font-medium">This thread is locked. No more replies allowed.</p>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            // Handle delete confirmation for replies v3.0 (AJAX)
            function deleteReply(replyId, deleteUrl) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#EF4444',
                    cancelButtonColor: '#3B82F6',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Use AJAX with DELETE method
                        fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => {
                                if (response.ok) {
                                    // Success - reload page
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Deleted!',
                                        text: 'Reply has been deleted.',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else if (response.status === 404) {
                                    // Reply not found
                                    Swal.fire({
                                        icon: 'info',
                                        title: 'Already Deleted',
                                        text: 'This reply has already been deleted or does not exist.',
                                        timer: 2000,
                                        showConfirmButton: false
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else if (response.status === 403) {
                                    // Unauthorized
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Unauthorized',
                                        text: 'You do not have permission to delete this reply.'
                                    });
                                } else {
                                    throw new Error('Delete failed');
                                }
                            })
                            .catch(error => {
                                console.error('Delete error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: 'Failed to delete reply. Please try again.'
                                });
                            });
                    }
                });
            }

            function toggleLike(type, id) {
                fetch('{{ route('forum.like') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            type,
                            id
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            const button = document.getElementById(`like-${type}-${id}`);
                            const countSpan = document.getElementById(`likes-count-${type}-${id}`);

                            if (data.liked) {
                                button.classList.add('bg-red-100', 'text-red-600');
                                button.classList.remove('bg-gray-100', 'text-gray-600');
                            } else {
                                button.classList.remove('bg-red-100', 'text-red-600');
                                button.classList.add('bg-gray-100', 'text-gray-600');
                            }

                            countSpan.textContent = data.likes_count;
                        }
                    });
            }

            function replyTo(replyId, userName) {
                document.getElementById('parent_id').value = replyId;
                document.getElementById('reply-to-name').textContent = userName;
                document.getElementById('reply-to-notice').style.display = 'block';
                document.getElementById('reply-content').focus();
            }

            function cancelReply() {
                document.getElementById('parent_id').value = '';
                document.getElementById('reply-to-notice').style.display = 'none';
            }
        </script>
    @endpush
</x-app-layout>
