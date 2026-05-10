<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $provider;
    protected $apiKey;
    protected $model;
    protected $maxTokens;
    protected $temperature;

    /**
     * Provider configurations.
     */
    protected array $providers = [
        'openai' => [
            'url' => 'https://api.openai.com/v1/chat/completions',
            'models' => [
                'gpt-4o' => 'GPT-4o (Terbaru, cepat & capable)',
                'gpt-4o-mini' => 'GPT-4o Mini (Hemat, cepat)',
                'gpt-4-turbo' => 'GPT-4 Turbo (Capable, cepat)',
                'gpt-4' => 'GPT-4 (Paling capable, lambat)',
                'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Hemat, cepat)',
            ],
        ],
        'anthropic' => [
            'url' => 'https://api.anthropic.com/v1/messages',
            'models' => [
                'claude-sonnet-4-20250514' => 'Claude Sonnet 4 (Terbaru, balanced)',
                'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Cepat & pintar)',
                'claude-3-5-haiku-20241022' => 'Claude 3.5 Haiku (Tercepat, hemat)',
                'claude-3-opus-20240229' => 'Claude 3 Opus (Paling capable)',
            ],
        ],
        'gemini' => [
            'url' => 'https://generativelanguage.googleapis.com/v1beta/models/',
            'models' => [
                'gemini-2.0-flash' => 'Gemini 2.0 Flash (Terbaru, cepat)',
                'gemini-1.5-pro' => 'Gemini 1.5 Pro (Capable, context panjang)',
                'gemini-1.5-flash' => 'Gemini 1.5 Flash (Cepat, hemat)',
            ],
        ],
    ];

    public function __construct()
    {
        $this->provider = Setting::get('ai_provider', 'openai');
        $this->apiKey = $this->resolveApiKey();
        $this->model = Setting::get('ai_model', 'gpt-3.5-turbo');
        $this->maxTokens = (int) Setting::get('ai_max_tokens', 1000);
        $this->temperature = (float) Setting::get('ai_temperature', 0.7);
    }

    /**
     * Resolve the API key based on the active provider.
     */
    protected function resolveApiKey(): string
    {
        return match ($this->provider) {
            'anthropic' => Setting::get('ai_anthropic_api_key', '') ?? '',
            'gemini' => Setting::get('ai_gemini_api_key', '') ?? '',
            default => Setting::get('ai_openai_api_key', '') ?? '',
        };
    }

    /**
     * Get available providers.
     */
    public function getProviders(): array
    {
        return [
            'openai' => 'OpenAI (GPT)',
            'anthropic' => 'Anthropic (Claude)',
            'gemini' => 'Google (Gemini)',
        ];
    }

    /**
     * Get models for a specific provider.
     */
    public function getModelsForProvider(string $provider): array
    {
        return $this->providers[$provider]['models'] ?? [];
    }

    /**
     * Get all models grouped by provider.
     */
    public function getAllModels(): array
    {
        $models = [];
        foreach ($this->providers as $provider => $config) {
            foreach ($config['models'] as $value => $label) {
                $models[$value] = $label;
            }
        }

        return $models;
    }

    /**
     * Check if AI is enabled and configured.
     */
    public function isEnabled(): bool
    {
        return Setting::get('ai_enabled', false) && !empty($this->apiKey);
    }

    /**
     * Get AI configuration status.
     */
    public function getStatus(): array
    {
        return [
            'enabled' => $this->isEnabled(),
            'provider' => $this->provider,
            'model' => $this->model,
            'has_api_key' => !empty($this->apiKey),
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
    }

    /**
     * Send a message and get AI response.
     */
    public function sendMessage(AiConversation $conversation, string $userMessage, array $context = []): AiMessage
    {
        if (!$this->isEnabled()) {
            throw new \Exception('AI Assistant is not enabled or configured.');
        }

        // Save user message
        $userMsg = $conversation->messages()->create([
            'role' => 'user',
            'content' => $userMessage,
            'tokens' => $this->estimateTokens($userMessage),
            'model' => $this->model,
        ]);

        // Build messages array
        $messages = $this->buildMessagesArray($conversation, $context);

        try {
            // Call the appropriate provider
            $result = match ($this->provider) {
                'anthropic' => $this->callAnthropic($messages),
                'gemini' => $this->callGemini($messages),
                default => $this->callOpenAI($messages),
            };

            // Save assistant message
            $assistantMsg = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $result['content'],
                'tokens' => $result['tokens'] ?? 0,
                'model' => $this->model,
                'metadata' => $result['metadata'] ?? [],
            ]);

            // Update conversation stats
            $conversation->updateStats();

            // Generate title if first message
            if ($conversation->message_count <= 2 && empty($conversation->title)) {
                $conversation->generateTitle();
            }

            return $assistantMsg;
        } catch (\Exception $e) {
            Log::error('AI Service Error', [
                'provider' => $this->provider,
                'message' => $e->getMessage(),
                'conversation_id' => $conversation->id,
            ]);
            throw $e;
        }
    }

    /**
     * Call OpenAI API.
     */
    protected function callOpenAI(array $messages): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => $messages,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ]);

        if ($response->failed()) {
            Log::error('OpenAI API Error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? 'No response',
            'tokens' => $data['usage']['completion_tokens'] ?? 0,
            'metadata' => [
                'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
                'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                'total_tokens' => $data['usage']['total_tokens'] ?? 0,
            ],
        ];
    }

    /**
     * Call Anthropic (Claude) API.
     */
    protected function callAnthropic(array $messages): array
    {
        // Separate system message from conversation messages
        $systemMessage = '';
        $conversationMessages = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemMessage .= $msg['content'] . "\n";
            } else {
                $conversationMessages[] = $msg;
            }
        }

        $payload = [
            'model' => $this->model,
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
            'messages' => $conversationMessages,
        ];

        if (!empty($systemMessage)) {
            $payload['system'] = trim($systemMessage);
        }

        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.anthropic.com/v1/messages', $payload);

        if ($response->failed()) {
            Log::error('Anthropic API Error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Anthropic API error: ' . $response->body());
        }

        $data = $response->json();

        $content = '';
        foreach ($data['content'] ?? [] as $block) {
            if ($block['type'] === 'text') {
                $content .= $block['text'];
            }
        }

        return [
            'content' => $content ?: 'No response',
            'tokens' => $data['usage']['output_tokens'] ?? 0,
            'metadata' => [
                'finish_reason' => $data['stop_reason'] ?? null,
                'prompt_tokens' => $data['usage']['input_tokens'] ?? 0,
                'total_tokens' => ($data['usage']['input_tokens'] ?? 0) + ($data['usage']['output_tokens'] ?? 0),
            ],
        ];
    }

    /**
     * Call Google Gemini API.
     */
    protected function callGemini(array $messages): array
    {
        // Convert messages to Gemini format
        $systemInstruction = '';
        $contents = [];

        foreach ($messages as $msg) {
            if ($msg['role'] === 'system') {
                $systemInstruction .= $msg['content'] . "\n";
            } else {
                $contents[] = [
                    'role' => $msg['role'] === 'assistant' ? 'model' : 'user',
                    'parts' => [['text' => $msg['content']]],
                ];
            }
        }

        $payload = [
            'contents' => $contents,
            'generationConfig' => [
                'maxOutputTokens' => $this->maxTokens,
                'temperature' => $this->temperature,
            ],
        ];

        if (!empty($systemInstruction)) {
            $payload['systemInstruction'] = [
                'parts' => [['text' => trim($systemInstruction)]],
            ];
        }

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->timeout(30)->post($url, $payload);

        if ($response->failed()) {
            Log::error('Gemini API Error', ['status' => $response->status(), 'body' => $response->body()]);
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        $data = $response->json();

        $content = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';
        $usageMetadata = $data['usageMetadata'] ?? [];

        return [
            'content' => $content,
            'tokens' => $usageMetadata['candidatesTokenCount'] ?? 0,
            'metadata' => [
                'finish_reason' => $data['candidates'][0]['finishReason'] ?? null,
                'prompt_tokens' => $usageMetadata['promptTokenCount'] ?? 0,
                'total_tokens' => $usageMetadata['totalTokenCount'] ?? 0,
            ],
        ];
    }

    /**
     * Build messages array for API.
     */
    protected function buildMessagesArray(AiConversation $conversation, array $context = []): array
    {
        $messages = [];

        // Add system message with context
        $systemMessage = $this->buildSystemMessage($conversation, $context);
        if ($systemMessage) {
            $messages[] = ['role' => 'system', 'content' => $systemMessage];
        }

        // Add conversation history (limit to last 10 messages)
        $history = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->take(10)
            ->get();

        foreach ($history as $message) {
            $messages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        return $messages;
    }

    /**
     * Build system message with context.
     */
    protected function buildSystemMessage(AiConversation $conversation, array $context = []): string
    {
        $systemMessages = [];

        // Custom system prompt from settings
        $customPrompt = Setting::get('ai_system_prompt', '');
        if (!empty($customPrompt)) {
            $systemMessages[] = $customPrompt;
        } else {
            $systemMessages[] = "You are a helpful AI teaching assistant for an online Learning Management System.";
            $systemMessages[] = "Your goal is to help students learn and understand course materials.";
            $systemMessages[] = "Be encouraging, patient, and provide clear explanations.";
            $systemMessages[] = "Respond in the same language as the student's message.";
        }

        // Add course context if available
        if ($conversation->course) {
            $course = $conversation->course;
            $systemMessages[] = "\n**Current Course Context:**";
            $systemMessages[] = "- Course: {$course->title}";
            $systemMessages[] = "- Description: {$course->description}";
        }

        // Add additional context
        if (!empty($context['material'])) {
            $systemMessages[] = "\n**Current Material:**";
            $systemMessages[] = $context['material'];
        }

        if (!empty($context['exam'])) {
            $systemMessages[] = "\n**Note:** Student is asking about an exam. Do not provide direct answers. Guide them to understand the concepts.";
        }

        // Guidelines
        $systemMessages[] = "\n**Guidelines:**";
        $systemMessages[] = "- Keep responses concise but informative";
        $systemMessages[] = "- Use examples when explaining concepts";
        $systemMessages[] = "- Encourage critical thinking";
        $systemMessages[] = "- Don't provide direct answers to assignments/exams";

        return implode("\n", $systemMessages);
    }

    /**
     * Estimate token count for a string.
     */
    protected function estimateTokens(string $text): int
    {
        return (int) ceil(strlen($text) / 4);
    }

    /**
     * Create a new conversation.
     */
    public function createConversation(int $userId, ?int $courseId = null, string $contextType = 'general', ?int $contextId = null): AiConversation
    {
        return AiConversation::create([
            'user_id' => $userId,
            'course_id' => $courseId,
            'context_type' => $contextType,
            'context_id' => $contextId,
            'title' => 'New Conversation',
        ]);
    }

    /**
     * Get user's recent conversations.
     */
    public function getUserConversations(int $userId, int $limit = 10)
    {
        return AiConversation::where('user_id', $userId)
            ->active()
            ->with('latestMessage')
            ->orderBy('last_message_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get conversation statistics.
     */
    public function getStatistics(): array
    {
        return [
            'total_conversations' => AiConversation::count(),
            'total_messages' => AiMessage::count(),
            'total_tokens_used' => AiConversation::sum('tokens_used'),
            'active_conversations' => AiConversation::active()->count(),
            'average_messages_per_conversation' => AiConversation::avg('message_count'),
        ];
    }

    /**
     * Test API connection for the active provider.
     */
    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'API key belum dikonfigurasi untuk provider ' . $this->provider,
            ];
        }

        try {
            return match ($this->provider) {
                'anthropic' => $this->testAnthropicConnection(),
                'gemini' => $this->testGeminiConnection(),
                default => $this->testOpenAIConnection(),
            };
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    protected function testOpenAIConnection(): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->timeout(10)->get('https://api.openai.com/v1/models');

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Koneksi OpenAI berhasil!',
                'models_available' => count($response->json('data', [])),
            ];
        }

        return ['success' => false, 'message' => 'Gagal koneksi: ' . $response->body()];
    }

    protected function testAnthropicConnection(): array
    {
        $response = Http::withHeaders([
            'x-api-key' => $this->apiKey,
            'anthropic-version' => '2023-06-01',
            'Content-Type' => 'application/json',
        ])->timeout(10)->post('https://api.anthropic.com/v1/messages', [
            'model' => 'claude-3-5-haiku-20241022',
            'max_tokens' => 10,
            'messages' => [['role' => 'user', 'content' => 'Hi']],
        ]);

        if ($response->successful()) {
            return [
                'success' => true,
                'message' => 'Koneksi Anthropic (Claude) berhasil!',
                'models_available' => count($this->providers['anthropic']['models']),
            ];
        }

        return ['success' => false, 'message' => 'Gagal koneksi: ' . $response->body()];
    }

    protected function testGeminiConnection(): array
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models?key={$this->apiKey}";

        $response = Http::timeout(10)->get($url);

        if ($response->successful()) {
            $models = $response->json('models', []);

            return [
                'success' => true,
                'message' => 'Koneksi Google Gemini berhasil!',
                'models_available' => count($models),
            ];
        }

        return ['success' => false, 'message' => 'Gagal koneksi: ' . $response->body()];
    }
}
