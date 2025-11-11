<?php

namespace App\Services;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected $apiKey;
    protected $model;
    protected $maxTokens;
    protected $temperature;
    protected $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = Setting::get('ai_openai_api_key', config('services.openai.api_key'));
        $this->model = Setting::get('ai_model', 'gpt-3.5-turbo');
        $this->maxTokens = (int) Setting::get('ai_max_tokens', 1000);
        $this->temperature = (float) Setting::get('ai_temperature', 0.7);
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
            'model' => $this->model,
            'has_api_key' => !empty($this->apiKey),
            'max_tokens' => $this->maxTokens,
            'temperature' => $this->temperature,
        ];
    }

    /**
     * Send a message and get AI response.
     *
     * @param AiConversation $conversation
     * @param string $userMessage
     * @param array $context Additional context for the AI
     * @return AiMessage
     * @throws \Exception
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

        // Build messages array for API
        $messages = $this->buildMessagesArray($conversation, $context);

        try {
            // Call OpenAI API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(30)->post($this->apiUrl, [
                'model' => $this->model,
                'messages' => $messages,
                'max_tokens' => $this->maxTokens,
                'temperature' => $this->temperature,
                'user' => 'user_' . $conversation->user_id,
            ]);

            if ($response->failed()) {
                Log::error('OpenAI API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                throw new \Exception('Failed to get response from AI: ' . $response->body());
            }

            $data = $response->json();

            // Save assistant message
            $assistantMsg = $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $data['choices'][0]['message']['content'] ?? 'No response',
                'tokens' => $data['usage']['completion_tokens'] ?? 0,
                'model' => $this->model,
                'metadata' => [
                    'finish_reason' => $data['choices'][0]['finish_reason'] ?? null,
                    'prompt_tokens' => $data['usage']['prompt_tokens'] ?? 0,
                    'total_tokens' => $data['usage']['total_tokens'] ?? 0,
                ],
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
                'message' => $e->getMessage(),
                'conversation_id' => $conversation->id,
            ]);
            throw $e;
        }
    }

    /**
     * Build messages array for OpenAI API.
     */
    protected function buildMessagesArray(AiConversation $conversation, array $context = []): array
    {
        $messages = [];

        // Add system message with context
        $systemMessage = $this->buildSystemMessage($conversation, $context);
        if ($systemMessage) {
            $messages[] = ['role' => 'system', 'content' => $systemMessage];
        }

        // Add conversation history (limit to last 10 messages to stay within token limits)
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

        // Base system message
        $systemMessages[] = "You are a helpful AI teaching assistant for an online Learning Management System.";
        $systemMessages[] = "Your goal is to help students learn and understand course materials.";
        $systemMessages[] = "Be encouraging, patient, and provide clear explanations.";

        // Add course context if available
        if ($conversation->course) {
            $course = $conversation->course;
            $systemMessages[] = "\n**Current Course Context:**";
            $systemMessages[] = "- Course: {$course->title}";
            $systemMessages[] = "- Description: {$course->description}";
            $systemMessages[] = "- Category: {$course->category?->name}";
        }

        // Add additional context
        if (!empty($context['material'])) {
            $systemMessages[] = "\n**Current Material:**";
            $systemMessages[] = $context['material'];
        }

        if (!empty($context['exam'])) {
            $systemMessages[] = "\n**Note:** Student is asking about an exam. Do not provide direct answers to exam questions. Instead, guide them to understand the concepts.";
        }

        // Add guidelines
        $systemMessages[] = "\n**Guidelines:**";
        $systemMessages[] = "- Keep responses concise but informative";
        $systemMessages[] = "- Use examples when explaining concepts";
        $systemMessages[] = "- Encourage critical thinking";
        $systemMessages[] = "- Don't provide direct answers to assignments/exams";
        $systemMessages[] = "- Suggest relevant course materials when appropriate";

        return implode("\n", $systemMessages);
    }

    /**
     * Estimate token count for a string (rough estimate).
     */
    protected function estimateTokens(string $text): int
    {
        // Rough estimate: ~4 characters per token
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
     * Test API connection.
     */
    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'API key is not configured.',
            ];
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(10)->get('https://api.openai.com/v1/models');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Connection successful!',
                    'models_available' => count($response->json('data', [])),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to connect: ' . $response->body(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }
}
