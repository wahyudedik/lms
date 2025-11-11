<?php

namespace App\Http\Controllers;

use App\Models\AiConversation;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAssistantController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->middleware('auth');
        $this->aiService = $aiService;
    }

    /**
     * Display AI assistant page.
     */
    public function index()
    {
        $conversations = $this->aiService->getUserConversations(Auth::id());
        $status = $this->aiService->getStatus();

        return view('ai.index', compact('conversations', 'status'));
    }

    /**
     * Show a specific conversation.
     */
    public function show(AiConversation $conversation)
    {
        // Ensure user owns this conversation
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to conversation');
        }

        $conversation->load('messages');
        $status = $this->aiService->getStatus();

        return view('ai.conversation', compact('conversation', 'status'));
    }

    /**
     * Create a new conversation.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'nullable|exists:courses,id',
            'context_type' => 'nullable|string|in:course,exam,material,general',
            'context_id' => 'nullable|integer',
            'message' => 'required|string|max:2000',
        ]);

        $conversation = $this->aiService->createConversation(
            Auth::id(),
            $validated['course_id'] ?? null,
            $validated['context_type'] ?? 'general',
            $validated['context_id'] ?? null
        );

        // Send first message
        try {
            $response = $this->aiService->sendMessage($conversation, $validated['message']);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => [
                    'id' => $response->id,
                    'role' => $response->role,
                    'content' => $response->content,
                    'created_at' => $response->created_at->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Send a message in an existing conversation.
     */
    public function sendMessage(Request $request, AiConversation $conversation)
    {
        // Ensure user owns this conversation
        if ($conversation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        try {
            $response = $this->aiService->sendMessage($conversation, $validated['message']);

            return response()->json([
                'success' => true,
                'message' => [
                    'id' => $response->id,
                    'role' => $response->role,
                    'content' => $response->content,
                    'created_at' => $response->created_at->toISOString(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get conversation messages.
     */
    public function messages(AiConversation $conversation)
    {
        // Ensure user owns this conversation
        if ($conversation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(fn($msg) => [
                'id' => $msg->id,
                'role' => $msg->role,
                'content' => $msg->content,
                'created_at' => $msg->created_at->toISOString(),
            ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    /**
     * Archive a conversation.
     */
    public function archive(AiConversation $conversation)
    {
        // Ensure user owns this conversation
        if ($conversation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->archive();

        return response()->json([
            'success' => true,
            'message' => 'Conversation archived successfully.',
        ]);
    }

    /**
     * Delete a conversation.
     */
    public function destroy(AiConversation $conversation)
    {
        // Ensure user owns this conversation
        if ($conversation->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Conversation deleted successfully.',
        ]);
    }

    /**
     * Get AI status.
     */
    public function status()
    {
        return response()->json($this->aiService->getStatus());
    }
}
