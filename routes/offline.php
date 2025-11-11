<?php

use App\Http\Controllers\OfflineExamController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Offline Routes
|--------------------------------------------------------------------------
|
| Routes for offline exam functionality
|
*/

Route::middleware(['auth'])->prefix('offline')->name('offline.')->group(function () {
    // Offline exam list
    Route::get('/exams', [OfflineExamController::class, 'index'])->name('exams.index');

    // Get exam data for caching
    Route::get('/exams/{exam}/data', [OfflineExamController::class, 'getExamData'])->name('exams.data');

    // Take offline exam
    Route::get('/exams/{exam}/take', [OfflineExamController::class, 'show'])->name('exams.take');

    // Save answer (works offline)
    Route::post('/exams/{exam}/answer', [OfflineExamController::class, 'saveAnswer'])->name('exams.answer');

    // Submit exam
    Route::post('/exams/{exam}/submit', [OfflineExamController::class, 'submit'])->name('exams.submit');

    // Get sync status
    Route::get('/sync/status', [OfflineExamController::class, 'getSyncStatus'])->name('sync.status');
});
