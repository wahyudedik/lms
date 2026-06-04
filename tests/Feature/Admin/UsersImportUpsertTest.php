<?php

namespace Tests\Feature\Admin;

use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('import updates password and details of existing user when password column is filled', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'test@example.com',
        'password' => Hash::make('old_password'),
        'role' => 'siswa',
        'phone' => '0800000000',
        'address' => 'Old Address',
    ]);

    $row = [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'test@example.com',
        'password' => 'new_password_123',
        'role' => 'siswa',
        'phone' => '08123456789',
        'birth_date' => '2000-01-01',
        'gender' => 'laki-laki',
        'address' => 'New Address',
        'status' => 'active',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);
    $result = $import->model($preparedRow);

    // It should return null (model updated in-place, not inserted as new)
    expect($result)->toBeNull();

    // Verify user details and password were updated
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect(Hash::check('new_password_123', $user->password))->toBeTrue();
    expect($user->phone)->toBe('08123456789');
    expect($user->address)->toBe('New Address');
    expect($user->birth_date->format('Y-m-d'))->toBe('2000-01-01');
    expect($user->gender)->toBe('laki-laki');
});

test('import does not update password of existing user if password column is blank', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'test@example.com',
        'password' => Hash::make('old_password'),
        'role' => 'siswa',
    ]);

    // password is empty string, which prepareForValidation converts to null
    $row = [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'test@example.com',
        'password' => '',
        'role' => 'siswa',
        'phone' => '08123456789',
        'birth_date' => '2000-01-01',
        'gender' => 'laki-laki',
        'address' => 'New Address',
        'status' => 'active',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);
    $result = $import->model($preparedRow);

    expect($result)->toBeNull();

    // Verify password is still the old password, but other details are updated
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect(Hash::check('old_password', $user->password))->toBeTrue();
});

test('import updates user by email if ID is not provided', function () {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'test@example.com',
        'password' => Hash::make('old_password'),
    ]);

    $row = [
        'id' => '',
        'name' => 'Updated Name',
        'email' => 'test@example.com',
        'password' => 'another_new_pass',
        'role' => 'siswa',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);
    $result = $import->model($preparedRow);

    expect($result)->toBeNull();

    // Verify user was updated
    $user->refresh();
    expect($user->name)->toBe('Updated Name');
    expect(Hash::check('another_new_pass', $user->password))->toBeTrue();
});

test('import creates new user with default password if password column is empty', function () {
    $row = [
        'id' => '',
        'name' => 'New User',
        'email' => 'new@example.com',
        'password' => '',
        'role' => 'siswa',
        'phone' => '08123456789',
        'birth_date' => '2005-05-05',
        'gender' => 'perempuan',
        'address' => 'Address New',
        'status' => 'active',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);
    $result = $import->model($preparedRow);

    // It should return a new User model instance
    expect($result)->toBeInstanceOf(User::class);
    expect($result->name)->toBe('New User');
    expect($result->email)->toBe('new@example.com');
    
    // Default password should be used
    $defaultPassword = config('app.default_user_password', 'LMS2024@Pass');
    expect(Hash::check($defaultPassword, $result->password))->toBeTrue();
});

test('import creates new user with custom password if password column is filled', function () {
    $row = [
        'id' => '',
        'name' => 'New User Custom Pass',
        'email' => 'new_custom@example.com',
        'password' => 'my_custom_secret_123',
        'role' => 'siswa',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);
    $result = $import->model($preparedRow);

    expect($result)->toBeInstanceOf(User::class);
    expect(Hash::check('my_custom_secret_123', $result->password))->toBeTrue();
});

test('import throws exception if trying to change email to an already taken email', function () {
    $user1 = User::factory()->create([
        'email' => 'user1@example.com',
    ]);
    $user2 = User::factory()->create([
        'email' => 'user2@example.com',
    ]);

    // Attempt to change user1's email to user2's email
    $row = [
        'id' => $user1->id,
        'name' => 'User One Name',
        'email' => 'user2@example.com',
        'password' => '',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);

    $this->expectException(\Exception::class);
    $this->expectExceptionMessage("Email 'user2@example.com' sudah digunakan oleh pengguna lain.");

    $import->model($preparedRow);
});

test('import normalizes capitalized role, status, and gender to match validation rules', function () {
    $row = [
        'id' => '',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'Mahasiswa',
        'gender' => 'Laki-laki',
        'status' => 'Active',
    ];

    $import = new UsersImport();
    $preparedRow = $import->prepareForValidation($row);

    // Assert that the prepared row has normalized lowercase values that pass validation rules
    expect($preparedRow['role'])->toBe('mahasiswa');
    expect($preparedRow['gender'])->toBe('laki-laki');
    expect($preparedRow['status'])->toBe('active');

    // Make sure we can validate using rules()
    $validator = \Illuminate\Support\Facades\Validator::make($preparedRow, $import->rules());
    expect($validator->fails())->toBeFalse();
});
