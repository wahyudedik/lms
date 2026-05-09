<?php

use App\Models\User;
use Tests\TestCase;

uses(TestCase::class);

/**
 * Feature: dosen-mahasiswa-role
 *
 * Validates: Requirements 1.3, 1.4
 */

/**
 * Property 1: Role method exclusivity
 *
 * For any role in {admin, guru, dosen, siswa, mahasiswa}, exactly one of
 * isAdmin(), isGuru(), isDosen(), isSiswa(), isMahasiswa() returns true.
 *
 * Validates: Requirements 1.3, 1.4
 */
it('ensures exactly one role method returns true for any role', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    $methods = ['isAdmin', 'isGuru', 'isDosen', 'isSiswa', 'isMahasiswa'];

    $trueCount = collect($methods)->filter(fn ($method) => $user->$method())->count();

    expect($trueCount)->toBe(1);
})->with(['admin', 'guru', 'dosen', 'siswa', 'mahasiswa']);

/**
 * Property 2: Role display completeness
 *
 * Untuk setiap role dalam {admin, guru, dosen, siswa, mahasiswa},
 * getRoleDisplayAttribute() return string non-empty yang unik per role.
 *
 * Validates: Requirements 1.5, 1.6
 */
it('returns a non-empty display string for each role', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    $display = $user->getRoleDisplayAttribute();

    expect($display)->toBeString()->not->toBeEmpty();
})->with(['admin', 'guru', 'dosen', 'siswa', 'mahasiswa']);

it('returns unique display strings across all roles', function () {
    $roles = ['admin', 'guru', 'dosen', 'siswa', 'mahasiswa'];

    $displays = collect($roles)->map(function (string $role) {
        $user = User::factory()->make(['role' => $role]);
        return $user->getRoleDisplayAttribute();
    });

    expect($displays->unique()->count())->toBe(count($roles));
});

/**
 * Property 3: Dashboard route completeness
 *
 * Untuk setiap role dalam {admin, guru, dosen, siswa, mahasiswa},
 * getDashboardRouteAttribute() return string non-null yang match pattern {role}.dashboard.
 *
 * Validates: Requirements 1.7, 1.8
 */
it('returns a non-null dashboard route string for each role', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    $route = $user->getDashboardRouteAttribute();

    expect($route)->toBeString()->not->toBeNull();
})->with(['admin', 'guru', 'dosen', 'siswa', 'mahasiswa']);

it('returns dashboard route matching {role}.dashboard pattern for each role', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    $route = $user->getDashboardRouteAttribute();

    expect($route)->toBe("{$role}.dashboard");
})->with(['admin', 'guru', 'dosen', 'siswa', 'mahasiswa']);

/**
 * Property 6: Role prefix mapping correctness
 *
 * Untuk setiap role dalam {guru, dosen, siswa, mahasiswa},
 * getRolePrefix() return string yang sama persis dengan nilai role-nya.
 *
 * Validates: Requirements 6.4
 */
it('returns the exact role string as prefix for each non-admin role', function (string $role) {
    $user = User::factory()->make(['role' => $role]);

    $prefix = $user->getRolePrefix();

    expect($prefix)->toBe($role);
})->with(['guru', 'dosen', 'siswa', 'mahasiswa']);
