<?php

use App\Http\Controllers\AiAssistantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AI Assistant Routes
|--------------------------------------------------------------------------
|
| Routes for AI-powered Q&A assistant functionality.
|
*/

Route::middleware(['auth', 'verified'])->prefix('ai')->name('ai.')->group(function () {
    // AI Assistant Pages
    Route::get('/', [AiAssistantController::class, 'index'])->name('index');
    Route::get('/conversations/{conversation}', [AiAssistantController::class, 'show'])->name('conversation.show');

    // API Endpoints
    Route::post('/conversations', [AiAssistantController::class, 'store'])->name('conversation.store');
    Route::post('/conversations/{conversation}/messages', [AiAssistantController::class, 'sendMessage'])->name('conversation.message');
    Route::get('/conversations/{conversation}/messages', [AiAssistantController::class, 'messages'])->name('conversation.messages');
    Route::post('/conversations/{conversation}/archive', [AiAssistantController::class, 'archive'])->name('conversation.archive');
    Route::delete('/conversations/{conversation}', [AiAssistantController::class, 'destroy'])->name('conversation.destroy');

    // Status Check
    Route::get('/status', [AiAssistantController::class, 'status'])->name('status');
});
