<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->logPath = storage_path('logs/laravel.log');
    
    // Backup original logs
    $this->originalLogContent = '';
    if (File::exists($this->logPath)) {
        $this->originalLogContent = File::get($this->logPath);
    }
    
    File::ensureDirectoryExists(dirname($this->logPath));
    File::put($this->logPath, ''); // Start with empty logs
});

afterEach(function () {
    // Restore original logs
    if ($this->originalLogContent !== '') {
        File::put($this->logPath, $this->originalLogContent);
    } else {
        if (File::exists($this->logPath)) {
            File::delete($this->logPath);
        }
    }
});

test('non-admin user cannot access error logs', function () {
    $student = User::factory()->create([
        'role' => 'siswa',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($student)
        ->get(route('admin.error-logs.index'));

    $response->assertStatus(403);
});

test('admin user can access error logs page and see logs list', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Write a dummy log entry
    $dummyLog = "[2026-06-04 10:00:00] local.ERROR: This is a dummy test error message\nStack trace line 1\nStack trace line 2\n";
    File::put($this->logPath, $dummyLog);

    $response = $this->actingAs($admin)
        ->get(route('admin.error-logs.index'));

    $response->assertStatus(200);
    $response->assertSee('Log Error Aplikasi');
    $response->assertSee('This is a dummy test error message');
    $response->assertSee('Stack trace line 1');
});

test('admin user can filter error logs by severity level', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Write dummy log entries with different levels
    $dummyLogs = "[2026-06-04 10:00:00] local.ERROR: Critical app crash message\n" .
                 "[2026-06-04 10:01:00] local.WARNING: Minor app warning message\n";
    File::put($this->logPath, $dummyLogs);

    // Filter by ERROR
    $response = $this->actingAs($admin)
        ->get(route('admin.error-logs.index', ['level' => 'ERROR']));
    
    $response->assertStatus(200);
    $response->assertSee('Critical app crash message');
    $response->assertDontSee('Minor app warning message');

    // Filter by WARNING
    $response = $this->actingAs($admin)
        ->get(route('admin.error-logs.index', ['level' => 'WARNING']));
        
    $response->assertStatus(200);
    $response->assertDontSee('Critical app crash message');
    $response->assertSee('Minor app warning message');
});

test('admin user can search error logs', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Write dummy log entries
    $dummyLogs = "[2026-06-04 10:00:00] local.ERROR: UniqueAppleKey crash message\n" .
                 "[2026-06-04 10:01:00] local.ERROR: UniqueBananaKey warning message\n";
    File::put($this->logPath, $dummyLogs);

    $response = $this->actingAs($admin)
        ->get(route('admin.error-logs.index', ['search' => 'Apple']));
    
    $response->assertStatus(200);
    $response->assertSee('UniqueAppleKey');
    $response->assertDontSee('UniqueBananaKey');
});

test('admin user can download error logs file', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    File::put($this->logPath, "Some log contents");

    $response = $this->actingAs($admin)
        ->get(route('admin.error-logs.download'));

    $response->assertStatus(200);
    $response->assertHeader('content-disposition', 'attachment; filename=laravel.log');
});

test('admin user can clear error logs file', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    File::put($this->logPath, "Some log contents to clear");
    expect(File::get($this->logPath))->toBe("Some log contents to clear");

    $response = $this->actingAs($admin)
        ->delete(route('admin.error-logs.clear'));

    $response->assertRedirect(route('admin.error-logs.index'));
    $response->assertSessionHas('success');
    expect(File::get($this->logPath))->toBe('');
});
