<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('admin can bulk delete other users', function () {
    // Create admin user
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Create three random users
    $users = User::factory()->count(3)->create([
        'is_active' => true,
    ]);
    $userIds = $users->pluck('id')->toArray();

    // Verify users exist in database
    expect(User::whereIn('id', $userIds)->count())->toBe(3);

    // Act as admin and call bulk destroy
    $response = $this->actingAs($admin)
        ->post(route('admin.users.bulk-destroy'), [
            'ids' => $userIds,
        ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success');

    // Assert that the users are deleted from database
    expect(User::whereIn('id', $userIds)->count())->toBe(0);
});

test('non-admin user cannot bulk delete users', function () {
    // Create student user
    $student = User::factory()->create([
        'role' => 'siswa',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Create three random users
    $users = User::factory()->count(3)->create([
        'is_active' => true,
    ]);
    $userIds = $users->pluck('id')->toArray();

    // Act as student and try bulk destroy
    $response = $this->actingAs($student)
        ->post(route('admin.users.bulk-destroy'), [
            'ids' => $userIds,
        ]);

    // Role middleware should reject access with 403
    $response->assertStatus(403);

    // Verify users are NOT deleted
    expect(User::whereIn('id', $userIds)->count())->toBe(3);
});

test('admin cannot bulk delete their own account', function () {
    // Create admin user
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Create another user
    $user = User::factory()->create([
        'is_active' => true,
    ]);

    // Send bulk destroy containing the admin's own ID
    $response = $this->actingAs($admin)
        ->post(route('admin.users.bulk-destroy'), [
            'ids' => [$admin->id, $user->id],
        ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('error', 'Anda tidak dapat menghapus akun Anda sendiri.');

    // Assert both users still exist
    expect(User::find($admin->id))->not->toBeNull();
    expect(User::find($user->id))->not->toBeNull();
});

test('bulk delete validation fails with invalid IDs', function () {
    // Create admin user
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Call with empty array and non-existent IDs
    $response = $this->actingAs($admin)
        ->post(route('admin.users.bulk-destroy'), [
            'ids' => [9999, 10000],
        ]);

    $response->assertSessionHasErrors('ids.0');
});

test('admin can bulk update class of other users', function () {
    // Create admin user
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Create a school class
    $schoolClass = \App\Models\SchoolClass::create([
        'name' => 'Kelas X',
        'education_level' => 'sma',
        'capacity' => 30,
    ]);

    // Create three users
    $users = User::factory()->count(3)->create([
        'is_active' => true,
        'school_class_id' => null,
    ]);
    $userIds = $users->pluck('id')->toArray();

    // Act as admin and call bulk update class
    $response = $this->actingAs($admin)
        ->post(route('admin.users.bulk-update-class'), [
            'ids' => $userIds,
            'school_class_id' => $schoolClass->id,
        ]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success');

    // Assert that the users have the new school_class_id
    expect(User::whereIn('id', $userIds)->where('school_class_id', $schoolClass->id)->count())->toBe(3);
});

test('non-admin user cannot bulk update class of users', function () {
    // Create student user
    $student = User::factory()->create([
        'role' => 'siswa',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Create a school class
    $schoolClass = \App\Models\SchoolClass::create([
        'name' => 'Kelas Y',
        'education_level' => 'sma',
        'capacity' => 30,
    ]);

    // Create three users
    $users = User::factory()->count(3)->create([
        'is_active' => true,
        'school_class_id' => null,
    ]);
    $userIds = $users->pluck('id')->toArray();

    // Act as student and try bulk update class
    $response = $this->actingAs($student)
        ->post(route('admin.users.bulk-update-class'), [
            'ids' => $userIds,
            'school_class_id' => $schoolClass->id,
        ]);

    $response->assertStatus(403);

    // Verify users are NOT updated
    expect(User::whereIn('id', $userIds)->where('school_class_id', $schoolClass->id)->count())->toBe(0);
});

test('bulk update class validation fails with invalid class or IDs', function () {
    // Create admin user
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    // Call with invalid class ID
    $response = $this->actingAs($admin)
        ->post(route('admin.users.bulk-update-class'), [
            'ids' => [1, 2],
            'school_class_id' => 9999, // non-existent class
        ]);

    $response->assertSessionHasErrors('school_class_id');
});

test('admin can update password of another user', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $user = User::factory()->create([
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.users.update-password', $user), [
            'password' => 'newPassword123!',
            'password_confirmation' => 'newPassword123!',
        ]);

    $response->assertRedirect(route('admin.users.edit', $user));
    $response->assertSessionHas('success');

    expect(\Illuminate\Support\Facades\Hash::check('newPassword123!', $user->refresh()->password))->toBeTrue();
});

test('admin can update their own password via admin panel', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $response = $this->actingAs($admin)
        ->patch(route('admin.users.update-password', $admin), [
            'password' => 'newPassword123!',
            'password_confirmation' => 'newPassword123!',
        ]);

    $response->assertRedirect(route('admin.users.edit', $admin));
    $response->assertSessionHas('success');
    $this->assertAuthenticatedAs($admin);

    expect(\Illuminate\Support\Facades\Hash::check('newPassword123!', $admin->refresh()->password))->toBeTrue();
});

test('admin can search users with empty role and status filters', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $student = User::factory()->create([
        'name' => 'Ahmad Harits',
        'email' => 'ahmad@example.com',
        'role' => 'mahasiswa',
        'is_active' => true,
    ]);

    // Request with search term but empty role and status
    $response = $this->actingAs($admin)
        ->get(route('admin.users.index', [
            'search' => 'Ahmad',
            'role' => '',
            'status' => '',
        ]));

    $response->assertStatus(200);
    $response->assertViewHas('users');
    
    $users = $response->viewData('users');
    expect($users->contains($student))->toBeTrue();
});


