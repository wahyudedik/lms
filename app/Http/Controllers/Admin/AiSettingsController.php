<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AiSettingsController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Display AI settings page.
     */
    public function index()
    {
        $settings = [
            'ai_enabled' => Setting::get('ai_enabled', false),
            'ai_openai_api_key' => Setting::get('ai_openai_api_key'),
            'ai_model' => Setting::get('ai_model', 'gpt-3.5-turbo'),
            'ai_max_tokens' => Setting::get('ai_max_tokens', 1000),
            'ai_temperature' => Setting::get('ai_temperature', 0.7),
            'ai_system_prompt' => Setting::get('ai_system_prompt', ''),
            'ai_rate_limit' => Setting::get('ai_rate_limit', 20), // messages per hour
            'ai_show_widget' => Setting::get('ai_show_widget', true),
        ];

        $stats = $this->aiService->getStatistics();
        $status = $this->aiService->getStatus();

        $models = [
            'gpt-4' => 'GPT-4 (Most capable, slower, more expensive)',
            'gpt-4-turbo-preview' => 'GPT-4 Turbo (Fast & capable)',
            'gpt-3.5-turbo' => 'GPT-3.5 Turbo (Fast, cost-effective)',
            'gpt-3.5-turbo-16k' => 'GPT-3.5 Turbo 16K (Longer context)',
        ];

        return view('admin.ai-settings.index', compact('settings', 'stats', 'status', 'models'));
    }

    /**
     * Update AI settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'ai_enabled' => 'boolean',
            'ai_openai_api_key' => 'nullable|string|min:20',
            'ai_model' => 'required|string',
            'ai_max_tokens' => 'required|integer|min:100|max:4000',
            'ai_temperature' => 'required|numeric|min:0|max:2',
            'ai_system_prompt' => 'nullable|string|max:1000',
            'ai_rate_limit' => 'required|integer|min:1|max:100',
            'ai_show_widget' => 'boolean',
        ]);

        foreach ($validated as $key => $value) {
            $type = match ($key) {
                'ai_enabled', 'ai_show_widget' => 'boolean',
                'ai_max_tokens', 'ai_rate_limit' => 'integer',
                'ai_temperature' => 'float',
                default => 'text',
            };

            Setting::set($key, $value, $type, 'ai');
        }

        // Clear cache
        Setting::clearCache();
        Artisan::call('config:clear');

        return redirect()
            ->route('admin.ai-settings.index')
            ->with('success', 'AI settings updated successfully! 🤖');
    }

    /**
     * Test OpenAI API connection.
     */
    public function testConnection()
    {
        $result = $this->aiService->testConnection();

        return response()->json($result);
    }

    /**
     * Reset AI settings to default.
     */
    public function reset()
    {
        Setting::set('ai_enabled', false, 'boolean', 'ai');
        Setting::set('ai_openai_api_key', null, 'text', 'ai');
        Setting::set('ai_model', 'gpt-3.5-turbo', 'text', 'ai');
        Setting::set('ai_max_tokens', 1000, 'integer', 'ai');
        Setting::set('ai_temperature', 0.7, 'float', 'ai');
        Setting::set('ai_system_prompt', '', 'text', 'ai');
        Setting::set('ai_rate_limit', 20, 'integer', 'ai');
        Setting::set('ai_show_widget', true, 'boolean', 'ai');

        // Clear cache
        Setting::clearCache();
        Artisan::call('config:clear');

        return redirect()
            ->route('admin.ai-settings.index')
            ->with('success', 'AI settings reset to default!');
    }

    /**
     * Get AI statistics for dashboard.
     */
    public function statistics()
    {
        $stats = $this->aiService->getStatistics();

        return response()->json([
            'success' => true,
            'statistics' => $stats,
        ]);
    }
}
