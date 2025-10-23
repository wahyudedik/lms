<div class="border-l-4 border-gray-200 {{ isset($depth) ? 'ml-' . $depth * 4 : '' }}">
    <div class="p-4 {{ $reply->is_solution ? 'bg-green-50' : '' }}">
        <!-- Reply Header -->
        <div class="flex items-start gap-4 mb-3">
            <div
                class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-purple-600 flex items-center justify-center text-white font-bold">
                {{ substr($reply->user->name, 0, 1) }}
            </div>
            <div class="flex-1">
                <div class="flex items-center gap-2 mb-1">
                    <span class="font-bold">{{ $reply->user->name }}</span>
                    <span class="text-xs text-gray-500">{{ $reply->created_at->diffForHumans() }}</span>
                    @if ($reply->is_solution)
                        <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-800">
                            <i class="fas fa-check-circle mr-1"></i>Solution
                        </span>
                    @endif
                </div>
                <!-- Reply Content -->
                <div class="prose prose-sm max-w-none mb-3">
                    {!! nl2br(e($reply->content)) !!}
                </div>

                <!-- Reply Actions -->
                <div class="flex items-center gap-3">
                    <button onclick="toggleLike('reply', {{ $reply->id }})" id="like-reply-{{ $reply->id }}"
                        class="text-sm flex items-center gap-1 px-2 py-1 rounded {{ $reply->isLikedBy(auth()->user()) ? 'text-red-600' : 'text-gray-600' }} hover:text-red-600">
                        <i class="fas fa-heart"></i>
                        <span id="likes-count-reply-{{ $reply->id }}">{{ $reply->likes_count }}</span>
                    </button>

                    @if (!$thread->is_locked || auth()->user()->isAdmin())
                        <button onclick="replyTo({{ $reply->id }}, '{{ $reply->user->name }}')"
                            class="text-sm text-gray-600 hover:text-indigo-600">
                            <i class="fas fa-reply mr-1"></i>Reply
                        </button>
                    @endif

                    @if (
                        !$reply->is_solution &&
                            ($thread->user_id === auth()->id() || auth()->user()->isAdmin() || auth()->user()->isGuru()))
                        <form action="{{ route('forum.solution', $reply->id) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-gray-600 hover:text-green-600">
                                <i class="fas fa-check mr-1"></i>Mark Solution
                            </button>
                        </form>
                    @endif

                    @if ($reply->user_id === auth()->id() || auth()->user()->isAdmin())
                        <button type="button"
                            onclick="deleteReply({{ $reply->id }}, '{{ route('forum.reply.destroy', $reply->id) }}')"
                            class="text-sm text-gray-600 hover:text-red-600">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Nested Replies -->
        @if ($reply->children->count() > 0)
            <div class="ml-6 mt-4 space-y-4 border-l-2 border-gray-100 pl-4">
                @foreach ($reply->children as $childReply)
                    @include('forum.partials.reply', [
                        'reply' => $childReply,
                        'thread' => $thread,
                        'depth' => ($depth ?? 0) + 1,
                    ])
                @endforeach
            </div>
        @endif
    </div>
</div>
