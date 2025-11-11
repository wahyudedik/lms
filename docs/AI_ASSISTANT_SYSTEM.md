# 🤖 AI Assistant System - Complete Guide

## Overview

The AI Assistant System integrates **ChatGPT** (OpenAI) into your Laravel LMS platform, providing students with intelligent, context-aware help for their courses, exams, and general learning questions.

## ✨ Features

### For Students
- **24/7 AI-Powered Q&A**: Get instant answers to course-related questions
- **Contextual Help**: AI understands the course you're studying
- **Conversation History**: Access past conversations
- **Floating Chat Widget**: Quick access from any page
- **Multi-topic Support**: Course help, exam guidance, study strategies
- **Natural Conversations**: Chat naturally like with a tutor

### For Administrators
- **Easy Configuration**: Simple API key setup
- **Model Selection**: Choose between GPT-4, GPT-3.5 Turbo, etc.
- **Cost Control**: Set token limits and rate limiting
- **Usage Analytics**: Track conversations, messages, and token usage
- **Customizable Behavior**: Adjust temperature, max tokens, system prompts
- **Enable/Disable Control**: Turn AI on/off system-wide

---

## 🚀 Setup Guide

### Step 1: Get OpenAI API Key

1. Go to [OpenAI Platform](https://platform.openai.com/)
2. Sign up or log in
3. Navigate to **API Keys** section
4. Click **"Create new secret key"**
5. Copy the key (starts with `sk-proj-...`)
6. **Important**: Save it securely - you won't be able to see it again!

### Step 2: Configure in LMS

1. Log in as **Admin**
2. Navigate to **Admin → AI Settings** (or `http://lms.test/admin/ai-settings`)
3. Paste your API key in the **OpenAI API Key** field
4. Check **"Enable AI Assistant"**
5. Choose your preferred model (recommended: `gpt-3.5-turbo` for cost-effectiveness)
6. Click **"Test Connection"** to verify
7. Click **"Save Settings"**

### Step 3: Configure Advanced Settings (Optional)

- **Max Tokens**: Limit response length (100-4000, default: 1000)
- **Temperature**: Control creativity (0-2, default: 0.7)
- **Rate Limit**: Messages per hour per user (default: 20)
- **System Prompt**: Customize AI behavior (leave empty for default)
- **Show Widget**: Display floating chat button (recommended: enabled)

---

## 📊 Database Schema

### `ai_conversations` Table

```php
id                  bigint UNSIGNED PRIMARY KEY
user_id             bigint UNSIGNED FOREIGN KEY -> users.id
course_id           bigint UNSIGNED NULLABLE FOREIGN KEY -> courses.id
title               varchar(255) NULLABLE
context_type        varchar(255) NULLABLE  // 'course', 'exam', 'material', 'general'
context_id          bigint UNSIGNED NULLABLE
message_count       int DEFAULT 0
tokens_used         int DEFAULT 0
last_message_at     timestamp NULLABLE
is_archived         boolean DEFAULT false
metadata            json NULLABLE
created_at          timestamp
updated_at          timestamp
```

### `ai_messages` Table

```php
id                  bigint UNSIGNED PRIMARY KEY
conversation_id     bigint UNSIGNED FOREIGN KEY -> ai_conversations.id
role                enum('user', 'assistant', 'system') DEFAULT 'user'
content             text
tokens              int DEFAULT 0
model               varchar(255) NULLABLE  // e.g., 'gpt-3.5-turbo'
metadata            json NULLABLE
sent_at             timestamp
created_at          timestamp
updated_at          timestamp
```

---

## 💻 Code Architecture

### Models

#### `AiConversation` Model
**Location**: `app/Models/AiConversation.php`

**Key Methods**:
- `messages()`: Get all messages in conversation
- `user()`: Get conversation owner
- `course()`: Get associated course
- `updateStats()`: Recalculate message count and tokens
- `generateTitle()`: Auto-generate title from first message
- `archive()`: Archive conversation
- `scopeActive($query)`: Get non-archived conversations

#### `AiMessage` Model
**Location**: `app/Models/AiMessage.php`

**Key Methods**:
- `conversation()`: Get parent conversation
- `isUser()`, `isAssistant()`, `isSystem()`: Check message role
- `scopeUser($query)`, `scopeAssistant($query)`: Filter by role

### Services

#### `AIService` Class
**Location**: `app/Services/AIService.php`

**Primary Service for AI Operations**

**Key Methods**:

```php
// Check if AI is enabled
isEnabled(): bool

// Send message and get response
sendMessage(AiConversation $conversation, string $message, array $context = []): AiMessage

// Create new conversation
createConversation(int $userId, ?int $courseId = null, string $contextType = 'general'): AiConversation

// Get user's conversations
getUserConversations(int $userId, int $limit = 10)

// Get usage statistics
getStatistics(): array

// Test OpenAI connection
testConnection(): array
```

**Example Usage**:

```php
use App\Services\AIService;

$aiService = app(AIService::class);

// Create conversation
$conversation = $aiService->createConversation(
    userId: auth()->id(),
    courseId: 1,
    contextType: 'course'
);

// Send message
$response = $aiService->sendMessage(
    conversation: $conversation,
    userMessage: "Explain object-oriented programming",
    context: ['material' => 'Chapter 3: OOP Basics']
);

echo $response->content; // AI's response
```

### Controllers

#### `AiAssistantController`
**Location**: `app/Http/Controllers/AiAssistantController.php`

**Routes**:
- `GET /ai` - Display AI assistant page
- `GET /ai/conversations/{conversation}` - Show conversation
- `POST /ai/conversations` - Create new conversation
- `POST /ai/conversations/{conversation}/messages` - Send message
- `GET /ai/conversations/{conversation}/messages` - Get messages
- `POST /ai/conversations/{conversation}/archive` - Archive conversation
- `DELETE /ai/conversations/{conversation}` - Delete conversation

#### `Admin\AiSettingsController`
**Location**: `app/Http/Controllers/Admin/AiSettingsController.php`

**Routes**:
- `GET /admin/ai-settings` - Settings page
- `POST /admin/ai-settings` - Update settings
- `POST /admin/ai-settings/test` - Test API connection
- `POST /admin/ai-settings/reset` - Reset to defaults

---

## 🎨 Frontend Components

### Views

#### Main AI Page
**Location**: `resources/views/ai/index.blade.php`
- Shows new conversation form
- Lists recent conversations
- Displays feature cards

#### Conversation Page
**Location**: `resources/views/ai/conversation.blade.php`
- Full chat interface
- Message history display
- Real-time message sending
- Archive/delete options

#### Admin Settings Page
**Location**: `resources/views/admin/ai-settings/index.blade.php`
- Configuration form
- Usage statistics cards
- Test connection button
- Model selection

### Floating Chat Widget
**Location**: `resources/views/components/ai-chat-widget.blade.php`

A floating button appears on all authenticated pages (if enabled):
- Click to open quick chat popup
- Start conversations instantly
- View recent messages
- Redirects to full AI page for detailed chat

**Auto-included in** `resources/views/layouts/app.blade.php`

---

## 🔐 Security & Best Practices

### API Key Security

✅ **DO**:
- Store API key in `.env` file
- Use Laravel's `Setting` model for encrypted storage
- Restrict access to AI settings (admin only)
- Rotate keys periodically

❌ **DON'T**:
- Commit API keys to Git
- Share keys publicly
- Use same key across environments

### Rate Limiting

The system includes built-in rate limiting:
- **Per User**: Configurable messages per hour (default: 20)
- **Token Limits**: Set max tokens per response (default: 1000)
- **Purpose**: Control costs and prevent abuse

### Content Safety

The AI assistant is configured with safety guidelines:
- Won't provide direct answers to exams
- Guides students to learn, not just copy
- Encourages critical thinking
- Suggests course materials when appropriate

---

## 💰 Cost Management

### Understanding Costs

OpenAI charges per token:
- **Input tokens**: Your messages + context
- **Output tokens**: AI's responses

**Approximate costs (GPT-3.5 Turbo)**:
- $0.0015 per 1K input tokens
- $0.002 per 1K output tokens

**Example**:
- Student asks 100-word question ≈ 125 tokens
- AI responds with 400-word answer ≈ 500 tokens
- Total cost: ~$0.0012 per conversation exchange

### Cost Optimization Tips

1. **Use GPT-3.5 Turbo**: 10x cheaper than GPT-4
2. **Set Token Limits**: Lower max_tokens reduces response length
3. **Enable Rate Limiting**: Prevent excessive usage
4. **Monitor Usage**: Check admin statistics dashboard
5. **Limit Context**: Only send relevant conversation history

### Monitoring Usage

Access statistics in **Admin → AI Settings**:
- Total conversations
- Total messages
- Tokens used (approx. cost)
- Active conversations
- Average messages per conversation

---

## 🎯 Use Cases

### 1. Course Q&A
**Student**: "Can you explain what inheritance is in OOP?"
**AI**: Provides detailed explanation with examples

### 2. Exam Preparation
**Student**: "I have an exam on data structures tomorrow. What should I study?"
**AI**: Lists key topics, study strategies, but doesn't provide direct answers

### 3. Problem Solving Guidance
**Student**: "I'm stuck on this SQL join problem."
**AI**: Guides thinking process, provides hints, not direct solutions

### 4. Study Strategies
**Student**: "How can I stay motivated when learning gets difficult?"
**AI**: Offers practical study techniques and motivation tips

### 5. General Learning Support
**Student**: "What are some effective ways to take notes?"
**AI**: Suggests various note-taking methods and best practices

---

## 🔧 Customization

### Custom System Prompts

Override default behavior by setting custom system prompts in **AI Settings**:

```text
You are a strict mathematics tutor who focuses on problem-solving techniques.
Always guide students to find answers themselves using the Socratic method.
Never provide direct solutions to homework problems.
```

### Context-Aware Responses

The AI automatically receives context about:
- Current course (if conversation is course-specific)
- Course description and category
- Context type (course/exam/material/general)

Enhance context programmatically:

```php
$aiService->sendMessage($conversation, $message, [
    'material' => $materialContent,
    'exam' => true, // Triggers "don't give direct answers" mode
]);
```

### Temperature Settings

- **0.0-0.3**: Focused, deterministic, factual
- **0.4-0.7**: Balanced (recommended for education)
- **0.8-1.0**: Creative, varied responses
- **1.1-2.0**: Very creative, unpredictable

---

## 📈 Analytics & Reporting

### Available Metrics

Access via `AIService::getStatistics()`:

```php
[
    'total_conversations' => 152,
    'total_messages' => 1048,
    'total_tokens_used' => 345621,
    'active_conversations' => 87,
    'average_messages_per_conversation' => 6.9
]
```

### Per-User Tracking

Each conversation stores:
- User ID (who initiated)
- Course ID (if course-specific)
- Token usage
- Message count
- Creation and last activity timestamps

---

## 🐛 Troubleshooting

### AI Assistant Not Working

**Problem**: "AI Assistant is Currently Unavailable"

**Solutions**:
1. Check if AI is enabled in **Admin → AI Settings**
2. Verify OpenAI API key is correct
3. Click **"Test Connection"** button
4. Check API key has not expired
5. Ensure billing is set up on OpenAI account
6. Check `.env` file if using environment variables

### Connection Test Fails

**Error**: "Failed to connect to OpenAI"

**Solutions**:
1. Verify internet connectivity
2. Check API key format (starts with `sk-proj-` or `sk-`)
3. Ensure no firewall blocking `api.openai.com`
4. Check OpenAI service status
5. Verify API key has not been revoked

### High Token Usage

**Problem**: Costs are too high

**Solutions**:
1. Lower **Max Tokens** (e.g., from 1000 to 500)
2. Reduce **Rate Limit** per user
3. Use **GPT-3.5 Turbo** instead of GPT-4
4. Limit conversation history sent to API
5. Monitor user behavior for abuse

### Widget Not Appearing

**Problem**: Floating chat button not visible

**Solutions**:
1. Check **"Show Floating Chat Widget"** is enabled
2. Verify user is authenticated
3. Clear browser cache
4. Check console for JavaScript errors
5. Ensure `app.blade.php` includes `<x-ai-chat-widget />`

---

## 🔄 Updates & Maintenance

### Updating OpenAI Models

When OpenAI releases new models:
1. Go to **Admin → AI Settings**
2. Select new model from dropdown
3. Test connection
4. Monitor initial usage for cost/performance
5. Adjust settings as needed

### Database Maintenance

Clean up old conversations:

```php
// Archive conversations older than 90 days
AiConversation::where('last_message_at', '<', now()->subDays(90))
    ->update(['is_archived' => true]);

// Delete archived conversations older than 1 year
AiConversation::where('is_archived', true)
    ->where('updated_at', '<', now()->subYear())
    ->delete();
```

### Backing Up Conversations

Include AI tables in backups:
- `ai_conversations`
- `ai_messages`

---

## 📚 API Reference

### Creating a Conversation

```php
POST /ai/conversations
Content-Type: application/json

{
    "message": "Can you help me with PHP?",
    "course_id": 5,  // Optional
    "context_type": "course",  // Optional: 'course', 'exam', 'material', 'general'
    "context_id": 5  // Optional
}

Response:
{
    "success": true,
    "conversation_id": 42,
    "message": {
        "id": 84,
        "role": "assistant",
        "content": "Of course! I'd be happy to help...",
        "created_at": "2025-10-26T12:34:56.000000Z"
    }
}
```

### Sending a Message

```php
POST /ai/conversations/{conversation}/messages
Content-Type: application/json

{
    "message": "What is a variable?"
}

Response:
{
    "success": true,
    "message": {
        "id": 85,
        "role": "assistant",
        "content": "A variable is a named container...",
        "created_at": "2025-10-26T12:35:22.000000Z"
    }
}
```

---

## 🎓 Educational Best Practices

### How AI Assists Learning

**✅ Good Use**:
- Explaining concepts
- Providing examples
- Suggesting study strategies
- Clarifying confusion
- Offering different perspectives

**❌ Inappropriate Use**:
- Getting direct exam answers
- Copying AI responses as homework
- Replacing actual study
- Avoiding learning challenges

### Guidelines for Students

Share these with students:

1. **Use AI as a tutor, not a shortcut**
2. **Ask for explanations, not just answers**
3. **Practice after understanding**
4. **Verify important information**
5. **Don't rely solely on AI**

---

## 🌟 Advanced Features

### Custom Conversation Context

```php
// In a controller
$conversation = $aiService->createConversation(
    userId: auth()->id(),
    courseId: $course->id,
    contextType: 'material',
    contextId: $material->id
);

// AI will have access to material context
$response = $aiService->sendMessage(
    $conversation,
    "Explain this concept",
    ['material' => $material->content]
);
```

### Programmatic AI Interaction

```php
// Batch processing
$questions = ['What is OOP?', 'What is inheritance?'];
$conversation = $aiService->createConversation(auth()->id());

foreach ($questions as $question) {
    $response = $aiService->sendMessage($conversation, $question);
    // Process response
}
```

---

## 📝 Changelog

### Version 1.0 (October 2025)
- ✅ Initial release
- ✅ OpenAI GPT integration
- ✅ Conversation management
- ✅ Admin settings panel
- ✅ Floating chat widget
- ✅ Usage statistics
- ✅ Rate limiting
- ✅ Context-aware responses

### Planned Features
- 🔜 Multi-language support
- 🔜 Voice input/output
- 🔜 Image analysis (GPT-4 Vision)
- 🔜 Code execution sandbox
- 🔜 Conversation sharing
- 🔜 Export conversations
- 🔜 Advanced analytics dashboard

---

## 🤝 Support

**Documentation**: `docs/AI_ASSISTANT_SYSTEM.md` (this file)

**For Administrators**: Access **Admin → AI Settings** for configuration

**For Developers**: Review code in:
- `app/Services/AIService.php`
- `app/Http/Controllers/AiAssistantController.php`
- `app/Models/AiConversation.php`
- `app/Models/AiMessage.php`

---

## 📄 License

This AI Assistant system is part of the Laravel LMS project.

**Note**: OpenAI API usage is subject to [OpenAI's Terms of Use](https://openai.com/policies/terms-of-use).

---

**Built with ❤️ for better learning experiences**

