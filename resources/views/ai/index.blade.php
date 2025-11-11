<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-gray-900">
            <i class="fas fa-robot mr-3"></i>AI Assistant
        </h2>
        <p class="text-sm text-gray-600 mt-1">Get instant help with your courses powered by ChatGPT</p>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (!$status['enabled'])
                <div class="bg-yellow-50 border-2 border-yellow-200 p-6 rounded-xl mb-8">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-3xl"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-yellow-900">AI Assistant is Currently Unavailable</h3>
                            <p class="text-sm text-yellow-700">Please contact your administrator to enable this feature.
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- New Conversation Card -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-8 rounded-xl shadow-lg text-white mb-8">
                    <div class="max-w-3xl mx-auto text-center">
                        <i class="fas fa-comments text-6xl mb-4 opacity-90"></i>
                        <h3 class="text-2xl font-bold mb-2">Start a New Conversation</h3>
                        <p class="text-blue-100 mb-6">Ask me anything about your courses, concepts, or learning
                            materials</p>
                        <form id="newConversationForm" class="max-w-2xl mx-auto">
                            <div class="flex gap-3">
                                <input type="text" id="firstMessage" placeholder="Type your question here..."
                                    class="flex-1 px-6 py-4 rounded-lg text-gray-900 focus:ring-4 focus:ring-blue-300"
                                    required>
                                <button type="submit"
                                    class="px-8 py-4 bg-white text-blue-600 rounded-lg hover:bg-blue-50 font-semibold">
                                    <i class="fas fa-paper-plane mr-2"></i>Send
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Recent Conversations -->
                @if ($conversations->count() > 0)
                    <div class="bg-white p-6 rounded-xl shadow-md">
                        <h3 class="text-xl font-bold mb-6 flex items-center">
                            <i class="fas fa-history mr-3 text-gray-600"></i>
                            Recent Conversations
                        </h3>
                        <div class="space-y-4">
                            @foreach ($conversations as $conversation)
                                <a href="{{ route('ai.conversation.show', $conversation) }}"
                                    class="block p-5 border border-gray-200 rounded-lg hover:shadow-lg hover:border-blue-300 transition">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h4 class="font-semibold text-gray-900 mb-1">
                                                {{ $conversation->title ?? 'Conversation #' . $conversation->id }}
                                            </h4>
                                            <div class="flex items-center gap-4 text-sm text-gray-500">
                                                <span>
                                                    <i class="fas fa-message mr-1"></i>
                                                    {{ $conversation->message_count }} messages
                                                </span>
                                                @if ($conversation->course)
                                                    <span>
                                                        <i class="fas fa-book mr-1"></i>
                                                        {{ $conversation->course->title }}
                                                    </span>
                                                @endif
                                                <span>
                                                    <i class="fas fa-clock mr-1"></i>
                                                    {{ $conversation->last_message_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-gray-400"></i>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-xl">
                        <i class="fas fa-comment-dots text-6xl text-gray-300 mb-4"></i>
                        <p class="text-gray-600 text-lg">No conversations yet. Start your first chat above!</p>
                    </div>
                @endif
            @endif

            <!-- Features Info -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-book-reader text-3xl text-blue-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Course Help</h4>
                    <p class="text-sm text-gray-600">Get explanations on course materials and concepts</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-lightbulb text-3xl text-purple-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">Problem Solving</h4>
                    <p class="text-sm text-gray-600">Get guidance (not answers) for assignments and problems</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-md text-center">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clock text-3xl text-green-600"></i>
                    </div>
                    <h4 class="font-semibold text-gray-900 mb-2">24/7 Availability</h4>
                    <p class="text-sm text-gray-600">Learn at your own pace, anytime you need help</p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.getElementById('newConversationForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const input = document.getElementById('firstMessage');
                const message = input.value.trim();

                if (!message) return;

                // Disable form
                input.disabled = true;
                const button = this.querySelector('button[type="submit"]');
                const originalButtonText = button.innerHTML;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Sending...';
                button.disabled = true;

                try {
                    const response = await fetch('{{ route('ai.conversation.store') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            message
                        })
                    });

                    const data = await response.json();

                    if (data.success) {
                        // Redirect to conversation
                        window.location.href = `/ai/conversations/${data.conversation_id}`;
                    } else {
                        throw new Error(data.error || 'Failed to send message');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to send message. Please try again.'
                    });

                    // Re-enable form
                    input.disabled = false;
                    button.innerHTML = originalButtonText;
                    button.disabled = false;
                }
            });
        </script>
    @endpush
</x-app-layout>
