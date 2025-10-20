<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Default dashboard (fallback)
Route::get('/dashboard', function () {
    $user = auth()->user();
    return redirect()->route($user->dashboard_route);
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Dashboard
Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware(['auth', 'verified', 'role:admin'])->name('admin.dashboard');

// Admin User Management
Route::middleware(['auth', 'verified', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Import/Export Routes (must be before resource routes)
    Route::get('users/export', [App\Http\Controllers\Admin\UserController::class, 'export'])->name('users.export');
    Route::get('users/import', [App\Http\Controllers\Admin\UserController::class, 'importForm'])->name('users.import');
    Route::post('users/import', [App\Http\Controllers\Admin\UserController::class, 'import'])->name('users.import.store');
    Route::get('users/template', [App\Http\Controllers\Admin\UserController::class, 'downloadTemplate'])->name('users.template');

    // Resource routes
    Route::resource('users', App\Http\Controllers\Admin\UserController::class);
    Route::post('users/{user}/toggle-status', [App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::patch('users/{user}/password', [App\Http\Controllers\Admin\UserController::class, 'updatePassword'])->name('users.update-password');
});

// Guru Dashboard
Route::get('/guru/dashboard', function () {
    return view('guru.dashboard');
})->middleware(['auth', 'verified', 'role:guru'])->name('guru.dashboard');

// Siswa Dashboard
Route::get('/siswa/dashboard', function () {
    return view('siswa.dashboard');
})->middleware(['auth', 'verified', 'role:siswa'])->name('siswa.dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Profile Photo Routes
    Route::post('/profile/photo/upload', [App\Http\Controllers\ProfilePhotoController::class, 'upload'])->name('profile.photo.upload');
    Route::delete('/profile/photo/delete', [App\Http\Controllers\ProfilePhotoController::class, 'delete'])->name('profile.photo.delete');
});

require __DIR__ . '/auth.php';
