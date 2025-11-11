<x-admin-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">
                    <i class="fas fa-robot mr-3"></i>AI Assistant Settings
                </h2>
                <p class="text-sm text-gray-600 mt-1">Configure ChatGPT integration for intelligent Q&A assistance</p>
            </div>
            <div class="flex gap-2">
                <button type="button" onclick="testConnection()"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plug mr-2"></i>Test Connection
                </button>
                <form action="{{ route('admin.ai-settings.reset') }}" method="POST"
                    onsubmit="return confirm('Reset all AI settings to default?');">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                        <i class="fas fa-undo mr-2"></i>Reset to Default
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span class="inline">{{ session('success') }}</span>
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <span class="inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6 rounded-xl shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm">Total Conversations</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($stats['total_conversations']) }}</p>
                        </div>
                        <i class="fas fa-comments text-4xl opacity-50"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-xl shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm">Total Messages</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($stats['total_messages']) }}</p>
                        </div>
                        <i class="fas fa-message text-4xl opacity-50"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-xl shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm">Tokens Used</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($stats['total_tokens_used']) }}</p>
                        </div>
                        <i class="fas fa-coins text-4xl opacity-50"></i>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-xl shadow-lg text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm">Active Chats</p>
                            <p class="text-3xl font-bold mt-1">{{ number_format($stats['active_conversations']) }}</p>
                        </div>
                        <i class="fas fa-bolt text-4xl opacity-50"></i>
                    </div>
                </div>
            </div>

            <!-- Status Banner -->
            <div
                class="mb-8 p-6 rounded-xl shadow-md {{ $status['enabled'] ? 'bg-green-50 border-2 border-green-200' : 'bg-yellow-50 border-2 border-yellow-200' }}">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-full {{ $status['enabled'] ? 'bg-green-100' : 'bg-yellow-100' }}">
                            <i
                                class="fas {{ $status['enabled'] ? 'fa-check-circle text-green-600' : 'fa-exclamation-triangle text-yellow-600' }} text-2xl"></i>
                        </div>
                        <div>
                            <h3
                                class="text-lg font-semibold {{ $status['enabled'] ? 'text-green-900' : 'text-yellow-900' }}">
                                AI Assistant is {{ $status['enabled'] ? 'Active' : 'Disabled' }}
                            </h3>
                            <p class="text-sm {{ $status['enabled'] ? 'text-green-700' : 'text-yellow-700' }}">
                                @if ($status['enabled'])
                                    Students can now use the AI assistant for learning support
                                @else
                                    Enable AI assistant to allow students to ask questions and get instant help
                                @endif
                            </p>
                        </div>
                    </div>
                    @if ($status['enabled'])
                        <span class="px-4 py-2 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
                            <i class="fas fa-circle text-xs text-green-500 mr-1 animate-pulse"></i>
                            Online
                        </span>
                    @endif
                </div>
            </div>

            <!-- Settings Form -->
            <form action="{{ route('admin.ai-settings.update') }}" method="POST" class="space-y-8">
                @csrf

                <!-- Basic Configuration -->
                <div class="bg-white p-8 rounded-xl shadow-md">
                    <h3 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
                        <i class="fas fa-cog mr-3 text-blue-600"></i>
                        Basic Configuration
                    </h3>

                    <div class="space-y-6">
                        <!-- Enable AI -->
                        <div class="flex items-start">
                            <input type="checkbox" name="ai_enabled" value="1" id="ai_enabled"
                                {{ old('ai_enabled', $settings['ai_enabled']) ? 'checked' : '' }}
                                class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm">
                            <div class="ml-3">
                                <label for="ai_enabled" class="text-sm font-medium text-gray-700">
                                    Enable AI Assistant
                                </label>
                                <p class="text-sm text-gray-500 mt-1">
                                    Allow students to use ChatGPT-powered Q&A assistant throughout the platform
                                </p>
                            </div>
                        </div>

                        <!-- Show Widget -->
                        <div class="flex items-start">
                            <input type="checkbox" name="ai_show_widget" value="1" id="ai_show_widget"
                                {{ old('ai_show_widget', $settings['ai_show_widget']) ? 'checked' : '' }}
                                class="mt-1 rounded border-gray-300 text-blue-600 shadow-sm">
                            <div class="ml-3">
                                <label for="ai_show_widget" class="text-sm font-medium text-gray-700">
                                    Show Floating Chat Widget
                                </label>
                                <p class="text-sm text-gray-500 mt-1">
                                    Display a floating chat button on all authenticated pages
                                </p>
                            </div>
                        </div>

                        <!-- API Key -->
                        <div>
                            <label for="ai_openai_api_key" class="block text-sm font-medium text-gray-700 mb-2">
                                OpenAI API Key <span class="text-red-500">*</span>
                            </label>
                            <input type="password" name="ai_openai_api_key" id="ai_openai_api_key"
                                value="{{ old('ai_openai_api_key', $settings['ai_openai_api_key']) }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="sk-proj-...">
                            <p class="text-sm text-gray-500 mt-1">
                                Get your API key from <a href="https://platform.openai.com/api-keys" target="_blank"
                                    class="text-blue-600 hover:underline">OpenAI Platform</a>
                            </p>
                            @error('ai_openai_api_key')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Model Selection -->
                        <div>
                            <label for="ai_model" class="block text-sm font-medium text-gray-700 mb-2">
                                AI Model <span class="text-red-500">*</span>
                            </label>
                            <select name="ai_model" id="ai_model"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @foreach ($models as $value => $label)
                                    <option value="{{ $value }}"
                                        {{ old('ai_model', $settings['ai_model']) == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-sm text-gray-500 mt-1">
                                Choose the AI model to use. GPT-4 is more capable but slower and expensive. GPT-3.5
                                Turbo is faster and cost-effective.
                            </p>
                            @error('ai_model')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Advanced Settings -->
                <div class="bg-white p-8 rounded-xl shadow-md">
                    <h3 class="text-xl font-bold mb-6 text-gray-900 flex items-center">
                        <i class="fas fa-sliders-h mr-3 text-purple-600"></i>
                        Advanced Settings
                    </h3>

                    <div class="space-y-6">
                        <!-- Max Tokens -->
                        <div>
                            <label for="ai_max_tokens" class="block text-sm font-medium text-gray-700 mb-2">
                                Maximum Tokens per Response
                            </label>
                            <input type="number" name="ai_max_tokens" id="ai_max_tokens"
                                value="{{ old('ai_max_tokens', $settings['ai_max_tokens']) }}" min="100"
                                max="4000"
                                class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">
                                Limit the length of AI responses (100-4000). Higher = longer responses but more
                                expensive.
                            </p>
                            @error('ai_max_tokens')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Temperature -->
                        <div>
                            <label for="ai_temperature" class="block text-sm font-medium text-gray-700 mb-2">
                                Temperature (Creativity)
                            </label>
                            <input type="number" name="ai_temperature" id="ai_temperature"
                                value="{{ old('ai_temperature', $settings['ai_temperature']) }}" min="0"
                                max="2" step="0.1"
                                class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">
                                Controls randomness. 0 = focused and deterministic, 2 = very creative. Recommended: 0.7
                            </p>
                            @error('ai_temperature')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Rate Limit -->
                        <div>
                            <label for="ai_rate_limit" class="block text-sm font-medium text-gray-700 mb-2">
                                Rate Limit (messages per hour per user)
                            </label>
                            <input type="number" name="ai_rate_limit" id="ai_rate_limit"
                                value="{{ old('ai_rate_limit', $settings['ai_rate_limit']) }}" min="1"
                                max="100"
                                class="w-full md:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <p class="text-sm text-gray-500 mt-1">
                                Limit how many messages each user can send per hour to control costs
                            </p>
                            @error('ai_rate_limit')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- System Prompt -->
                        <div>
                            <label for="ai_system_prompt" class="block text-sm font-medium text-gray-700 mb-2">
                                Custom System Prompt (Optional)
                            </label>
                            <textarea name="ai_system_prompt" id="ai_system_prompt" rows="4"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Leave empty to use default prompt...">{{ old('ai_system_prompt', $settings['ai_system_prompt']) }}</textarea>
                            <p class="text-sm text-gray-500 mt-1">
                                Customize the AI's behavior and personality. Leave empty to use the default educational
                                assistant prompt.
                            </p>
                            @error('ai_system_prompt')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Save Button -->
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </a>
                    <button type="submit" class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                        <i class="fas fa-save mr-2"></i>Save Settings
                    </button>
                </div>
            </form>

            <!-- Help Section -->
            <div class="mt-8 bg-gradient-to-r from-blue-50 to-purple-50 p-8 rounded-xl border border-blue-200">
                <h3 class="text-lg font-bold text-gray-900 mb-4">
                    <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>
                    Getting Started with AI Assistant
                </h3>
                <div class="space-y-3 text-sm text-gray-700">
                    <p><strong>1.</strong> Get your OpenAI API key from <a href="https://platform.openai.com/api-keys"
                            target="_blank"
                            class="text-blue-600 hover:underline">https://platform.openai.com/api-keys</a></p>
                    <p><strong>2.</strong> Paste the API key above and enable the AI assistant</p>
                    <p><strong>3.</strong> Click "Test Connection" to verify your API key works</p>
                    <p><strong>4.</strong> Configure advanced settings like model, tokens, and temperature</p>
                    <p><strong>5.</strong> Students can now access AI assistant via the chat widget or dedicated AI page
                    </p>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            function testConnection() {
                const button = event.target;
                const originalText = button.innerHTML;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Testing...';

                fetch('{{ route('admin.ai-settings.test') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        button.disabled = false;
                        button.innerHTML = originalText;

                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Connection Successful!',
                                html: data.message + '<br><small>' + data.models_available +
                                    ' models available</small>',
                                timer: 3000
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Connection Failed',
                                text: data.message
                            });
                        }
                    })
                    .catch(error => {
                        button.disabled = false;
                        button.innerHTML = originalText;
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to test connection: ' + error.message
                        });
                    });
            }
        </script>
    @endpush
</x-admin-layout>
