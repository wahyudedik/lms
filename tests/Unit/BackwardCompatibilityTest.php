<?php

use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

/**
 * Feature: dosen-mahasiswa-role
 *
 * Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7
 */

/**
 * Property 8: Backward compatibility — existing roles unaffected
 *
 * Untuk user dengan role `guru` atau `siswa`, semua role method checks
 * menghasilkan hasil yang sama seperti sebelum fitur ini diimplementasikan.
 *
 * Validates: Requirements 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7
 */

// isGuru() masih return true untuk guru
it('isGuru() returns true for user with role guru', function () {
    $user = User::factory()->make(['role' => 'guru']);

    expect($user->isGuru())->toBeTrue();
});

// isSiswa() masih return true untuk siswa
it('isSiswa() returns true for user with role siswa', function () {
    $user = User::factory()->make(['role' => 'siswa']);

    expect($user->isSiswa())->toBeTrue();
});

// getRolePrefix() masih return "guru" untuk guru dan "siswa" untuk siswa
it('getRolePrefix() returns the correct prefix for guru and siswa', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    expect($user->getRolePrefix())->toBe($role);
})->with(['guru', 'siswa']);

// isDosen() return false untuk guru dan siswa
it('isDosen() returns false for guru and siswa', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    expect($user->isDosen())->toBeFalse();
})->with(['guru', 'siswa']);

// isMahasiswa() return false untuk guru dan siswa
it('isMahasiswa() returns false for guru and siswa', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    expect($user->isMahasiswa())->toBeFalse();
})->with(['guru', 'siswa']);

// only isGuru() returns true for guru — all other role methods return false
it('only isGuru() returns true for user with role guru', function () {
    $user = User::factory()->make(['role' => 'guru']);

    expect($user->isGuru())->toBeTrue()
        ->and($user->isSiswa())->toBeFalse()
        ->and($user->isDosen())->toBeFalse()
        ->and($user->isMahasiswa())->toBeFalse()
        ->and($user->isAdmin())->toBeFalse();
});

// only isSiswa() returns true for siswa — all other role methods return false
it('only isSiswa() returns true for user with role siswa', function () {
    $user = User::factory()->make(['role' => 'siswa']);

    expect($user->isSiswa())->toBeTrue()
        ->and($user->isGuru())->toBeFalse()
        ->and($user->isDosen())->toBeFalse()
        ->and($user->isMahasiswa())->toBeFalse()
        ->and($user->isAdmin())->toBeFalse();
});
