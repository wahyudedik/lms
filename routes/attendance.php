<?php

use App\Http\Controllers\MaterialAttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Attendance Routes (Absensi per materi per tanggal)
|--------------------------------------------------------------------------
|
| Satu controller bersama (root namespace) untuk semua role. Middleware group
| mengikuti pola route lain di masing-masing role.
|
*/

// Admin (dengan log.admin)
Route::middleware(['auth', 'verified', 'role:admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('courses/{course}/materials/{material}/attendance', [MaterialAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('courses/{course}/materials/{material}/attendance', [MaterialAttendanceController::class, 'save'])->name('attendance.save');
    Route::get('courses/{course}/attendance/report', [MaterialAttendanceController::class, 'report'])->name('attendance.report');
    Route::get('courses/{course}/attendance/export', [MaterialAttendanceController::class, 'exportExcel'])->name('attendance.export');
});

// Guru
Route::middleware(['auth', 'verified', 'role:guru'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('courses/{course}/materials/{material}/attendance', [MaterialAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('courses/{course}/materials/{material}/attendance', [MaterialAttendanceController::class, 'save'])->name('attendance.save');
    Route::get('courses/{course}/attendance/report', [MaterialAttendanceController::class, 'report'])->name('attendance.report');
    Route::get('courses/{course}/attendance/export', [MaterialAttendanceController::class, 'exportExcel'])->name('attendance.export');
});

// Dosen (equivalence guru)
Route::middleware(['auth', 'verified', 'role:dosen'])->prefix('dosen')->name('dosen.')->group(function () {
    Route::get('courses/{course}/materials/{material}/attendance', [MaterialAttendanceController::class, 'index'])->name('attendance.index');
    Route::post('courses/{course}/materials/{material}/attendance', [MaterialAttendanceController::class, 'save'])->name('attendance.save');
    Route::get('courses/{course}/attendance/report', [MaterialAttendanceController::class, 'report'])->name('attendance.report');
    Route::get('courses/{course}/attendance/export', [MaterialAttendanceController::class, 'exportExcel'])->name('attendance.export');
});

// Siswa
Route::middleware(['auth', 'verified', 'role:siswa'])->prefix('siswa')->name('siswa.')->group(function () {
    Route::get('attendance', [MaterialAttendanceController::class, 'myAttendance'])->name('attendance.index');
});

// Mahasiswa (equivalence siswa)
Route::middleware(['auth', 'verified', 'role:mahasiswa'])->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
    Route::get('attendance', [MaterialAttendanceController::class, 'myAttendance'])->name('attendance.index');
});
