<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('ai.index') }}" class="text-gray-600 hover:text-gray-900">
                    <i class="fas fa-arrow-left text-xl"></i>
                </a>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">
                        {{ $conversation->title ?? 'AI Assistant' }}
                    </h2>
                    <p class="text-sm text-gray-600">
                        @if ($conversation->course)
                            <i class="fas fa-book mr-1"></i>{{ $conversation->course->title }}
                        @else
                            <i class="fas fa-robot mr-1"></i>General conversation
                        @endif
                    </p>
                </div>
            </div>
            <div class="flex gap-2">
                <button onclick="archiveConversation()"
                    class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    <i class="fas fa-archive mr-2"></i>Archive
                </button>
                <button onclick="deleteConversation()"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (!$status['enabled'])
                <div class="bg-yellow-50 border-2 border-yellow-200 p-6 rounded-xl">
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
                <!-- Chat Container -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden"
                    style="height: calc(100vh - 250px); min-height: 500px;">

                    <!-- Messages Area -->
                    <div id="messagesContainer" class="overflow-y-auto p-6 space-y-6"
                        style="height: calc(100% - 80px);">
                        @foreach ($conversation->messages as $message)
                            <div
                                class="message-item flex {{ $message->role === 'user' ? 'justify-end' : 'justify-start' }}">
                                <div
                                    class="flex gap-3 max-w-3xl {{ $message->role === 'user' ? 'flex-row-reverse' : '' }}">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        @if ($message->role === 'user')
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">
                                                {{ substr(auth()->user()->name, 0, 1) }}
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white flex items-center justify-center">
                                                <i class="fas fa-robot"></i>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Message Bubble -->
                                    <div class="flex-1">
                                        <div
                                            class="text-xs text-gray-500 mb-1 {{ $message->role === 'user' ? 'text-right' : '' }}">
                                            {{ $message->role === 'user' ? 'You' : 'AI Assistant' }} ·
                                            {{ $message->created_at->diffForHumans() }}
                                        </div>
                                        <div
                                            class="p-4 rounded-2xl {{ $message->role === 'user' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900' }}">
                                            <div
                                                class="prose prose-sm max-w-none {{ $message->role === 'user' ? 'prose-invert' : '' }}">
                                                {!! nl2br(e($message->content)) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <!-- Typing Indicator (hidden by default) -->
                        <div id="typingIndicator" class="flex justify-start hidden">
                            <div class="flex gap-3 max-w-3xl">
                                <div class="flex-shrink-0">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white flex items-center justify-center">
                                        <i class="fas fa-robot"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="p-4 rounded-2xl bg-gray-100 text-gray-900">
                                        <div class="flex gap-1">
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                                style="animation-delay: 0.1s"></div>
                                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                                style="animation-delay: 0.2s"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Input Area -->
                    <div class="border-t border-gray-200 p-4 bg-gray-50">
                        <form id="messageForm" class="flex gap-3">
                            <input type="text" id="messageInput" placeholder="Type your message..."
                                class="flex-1 px-4 py-3 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                required>
                            <button type="submit"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-semibold">
                                <i class="fas fa-paper-plane mr-2"></i>Send
                            </button>
                        </form>
                        <p class="text-xs text-gray-500 mt-2 text-center">
                            <i class="fas fa-info-circle mr-1"></i>
                            AI can make mistakes. Please verify important information.
                        </p>
                    </div>

                </div>

                <!-- Stats -->
                <div class="mt-4 flex items-center justify-between text-sm text-gray-600">
                    <span>
                        <i class="fas fa-message mr-1"></i>
                        {{ $conversation->message_count }} messages
                    </span>
                    <span>
                        <i class="fas fa-coins mr-1"></i>
                        {{ number_format($conversation->tokens_used) }} tokens used
                    </span>
                    <span>
                        <i class="fas fa-clock mr-1"></i>
                        Last activity: {{ $conversation->last_message_at->diffForHumans() }}
                    </span>
                </div>
            @endif

        </div>
    </div>

    @push('scripts')
        <script>
            const conversationId = {{ $conversation->id }};

            // Scroll to bottom on load
            document.addEventListener('DOMContentLoaded', function() {
                scrollToBottom();
            });

            // Handle message form submission
            document.getElementById('messageForm')?.addEventListener('submit', async function(e) {
                e.preventDefault();

                const input = document.getElementById('messageInput');
                const message = input.value.trim();

                if (!message) return;

                // Add user message to UI immediately
                addMessageToUI('user', message);
                input.value = '';

                // Show typing indicator
                showTypingIndicator();

                // Disable form
                input.disabled = true;
                const button = this.querySelector('button[type="submit"]');
                button.disabled = true;

                try {
                    const response = await fetch(`/ai/conversations/${conversationId}/messages`, {
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

                    // Hide typing indicator
                    hideTypingIndicator();

                    if (data.success) {
                        // Add assistant message to UI
                        addMessageToUI('assistant', data.message.content);
                    } else {
                        throw new Error(data.error || 'Failed to send message');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    hideTypingIndicator();

                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Failed to send message. Please try again.'
                    });
                } finally {
                    // Re-enable form
                    input.disabled = false;
                    button.disabled = false;
                    input.focus();
                }
            });

            function addMessageToUI(role, content) {
                const container = document.getElementById('messagesContainer');
                const isUser = role === 'user';

                const messageDiv = document.createElement('div');
                messageDiv.className = `message-item flex ${isUser ? 'justify-end' : 'justify-start'}`;

                messageDiv.innerHTML = `
                <div class="flex gap-3 max-w-3xl ${isUser ? 'flex-row-reverse' : ''}">
                    <div class="flex-shrink-0">
                        ${isUser 
                            ? `<div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-semibold">
                                        ${('{{ substr(auth()->user()->name, 0, 1) }}')}
                                       </div>`
                            : `<div class="w-10 h-10 rounded-full bg-gradient-to-r from-purple-500 to-blue-500 text-white flex items-center justify-center">
                                        <i class="fas fa-robot"></i>
                                       </div>`
                        }
                    </div>
                    <div class="flex-1">
                        <div class="text-xs text-gray-500 mb-1 ${isUser ? 'text-right' : ''}">
                            ${isUser ? 'You' : 'AI Assistant'} · just now
                        </div>
                        <div class="p-4 rounded-2xl ${isUser ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-900'}">
                            <div class="prose prose-sm max-w-none ${isUser ? 'prose-invert' : ''}">
                                ${content.replace(/\n/g, '<br>')}
                            </div>
                        </div>
                    </div>
                </div>
            `;

                container.appendChild(messageDiv);
                scrollToBottom();
            }

            function scrollToBottom() {
                const container = document.getElementById('messagesContainer');
                container.scrollTop = container.scrollHeight;
            }

            function showTypingIndicator() {
                document.getElementById('typingIndicator').classList.remove('hidden');
                scrollToBottom();
            }

            function hideTypingIndicator() {
                document.getElementById('typingIndicator').classList.add('hidden');
            }

            async function archiveConversation() {
                const result = await Swal.fire({
                    title: 'Archive Conversation?',
                    text: 'You can still access archived conversations later.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, archive it',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/ai/conversations/${conversationId}/archive`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Archived!',
                                text: 'Conversation has been archived.',
                                timer: 1500
                            }).then(() => {
                                window.location.href = '{{ route('ai.index') }}';
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to archive conversation.'
                        });
                    }
                }
            }

            async function deleteConversation() {
                const result = await Swal.fire({
                    title: 'Delete Conversation?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'
                });

                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/ai/conversations/${conversationId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Conversation has been deleted.',
                                timer: 1500
                            }).then(() => {
                                window.location.href = '{{ route('ai.index') }}';
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete conversation.'
                        });
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
