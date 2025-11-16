@php
    $aiEnabled = \App\Models\Setting::get('ai_enabled', false);
    $showWidget = \App\Models\Setting::get('ai_show_widget', true);
@endphp

@if (auth()->check() && $aiEnabled && $showWidget)
    <!-- Floating AI Chat Widget -->
    <div id="aiChatWidget" class="fixed bottom-6 right-6 z-50">
        <!-- Chat Button -->
        <button onclick="toggleAIWidget()"
            class="w-14 h-14 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-110 transition-all flex items-center justify-center">
            <i class="fas fa-robot text-2xl"></i>
        </button>

        <!-- Chat Popup (hidden by default) -->
        <div id="aiChatPopup"
            class="hidden absolute bottom-20 right-0 w-96 bg-white rounded-xl shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-robot text-2xl"></i>
                        <div>
                            <h3 class="font-semibold">{{ __('AI Assistant') }}</h3>
                            <p class="text-xs text-blue-100">{{ __('Powered by ChatGPT') }}</p>
                        </div>
                    </div>
                    <button onclick="closeAIWidget()" class="text-white hover:text-blue-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="p-4 bg-gray-50 border-b">
                <p class="text-sm text-gray-600 mb-3">{{ __('Quick actions:') }}</p>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('ai.index') }}"
                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs hover:bg-blue-200">
                        <i class="fas fa-comments mr-1"></i>{{ __('View All Chats') }}
                    </a>
                    <button onclick="startQuickChat()"
                        class="px-3 py-1 bg-purple-100 text-purple-700 rounded-full text-xs hover:bg-purple-200">
                        <i class="fas fa-plus mr-1"></i>{{ __('New Chat') }}
                    </button>
                </div>
            </div>

            <!-- Mini Messages -->
            <div id="aiWidgetMessages" class="h-64 overflow-y-auto p-4 space-y-3">
                <div class="text-center text-gray-500 text-sm py-8">
                    <i class="fas fa-comment-dots text-4xl mb-2 text-gray-300"></i>
                    <p>{{ __('Click ":button" to start a conversation', ['button' => __('New Chat')]) }}</p>
                </div>
            </div>

            <!-- Quick Input -->
            <div class="p-3 bg-gray-50 border-t">
                <form id="aiWidgetForm" class="flex gap-2" onsubmit="sendQuickMessage(event)">
                    <input type="text" id="aiWidgetInput" placeholder="{{ __('Ask me anything...') }}"
                        class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            const aiWidgetLocale = {
                sendError: @json(__('Failed to send message. Please try again.')),
            };

            function toggleAIWidget() {
                const popup = document.getElementById('aiChatPopup');
                popup.classList.toggle('hidden');
            }

            function closeAIWidget() {
                document.getElementById('aiChatPopup').classList.add('hidden');
            }

            function startQuickChat() {
                window.location.href = '{{ route('ai.index') }}';
            }

            async function sendQuickMessage(event) {
                event.preventDefault();

                const input = document.getElementById('aiWidgetInput');
                const message = input.value.trim();

                if (!message) return;

                // Redirect to full AI page for now (can be enhanced to handle in widget)
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
                        window.location.href = `/ai/conversations/${data.conversation_id}`;
                    }
                } catch (error) {
                    console.error('Error:', error);
                    alert(aiWidgetLocale.sendError);
                }
            }

            // Close widget when clicking outside
            document.addEventListener('click', function(event) {
                const widget = document.getElementById('aiChatWidget');
                const popup = document.getElementById('aiChatPopup');

                if (widget && !widget.contains(event.target) && !popup.classList.contains('hidden')) {
                    closeAIWidget();
                }
            });
        </script>
    @endpush
@endif
