<?php

use App\Http\Controllers\CertificateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Certificate Routes
|--------------------------------------------------------------------------
*/

// Public certificate verification
Route::get('/verify-certificate/{certificateNumber?}', [CertificateController::class, 'verify'])
    ->name('certificates.verify');

// Public certificate view
Route::get('/certificates/{certificateNumber}', [CertificateController::class, 'show'])
    ->name('certificates.show');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    // User certificates
    Route::get('/my-certificates', [CertificateController::class, 'index'])
        ->name('certificates.index');

    // Download certificate
    Route::get('/certificates/{certificateNumber}/download', [CertificateController::class, 'download'])
        ->name('certificates.download');

    // View certificate PDF
    Route::get('/certificates/{certificateNumber}/view', [CertificateController::class, 'view'])
        ->name('certificates.view');

    // Generate certificate for enrollment
    Route::post('/enrollments/{enrollment}/certificate', [CertificateController::class, 'generate'])
        ->name('certificates.generate');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    // Admin certificate management
    Route::get('/certificates', [CertificateController::class, 'adminIndex'])
        ->name('admin.certificates.index');

    // Revoke certificate
    Route::post('/certificates/{certificate}/revoke', [CertificateController::class, 'revoke'])
        ->name('admin.certificates.revoke');

    // Restore certificate
    Route::post('/certificates/{certificate}/restore', [CertificateController::class, 'restore'])
        ->name('admin.certificates.restore');

    // Generate missing certificates
    Route::post('/certificates/generate-missing', [CertificateController::class, 'generateMissing'])
        ->name('admin.certificates.generate-missing');
});

// API routes
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    // Get user certificates
    Route::get('/certificates', [CertificateController::class, 'apiIndex'])
        ->name('api.certificates.index');

    // Verify certificate
    Route::post('/certificates/verify', [CertificateController::class, 'apiVerify'])
        ->name('api.certificates.verify');
});
