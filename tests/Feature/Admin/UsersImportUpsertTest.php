<?php

namespace Tests\Feature\Admin;

use App\Imports\UsersImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(RefreshDatabase::class);

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

/**
 * Membuat file XLSX sungguhan untuk diupload ke route admin.users.import.store.
 * $dataRows: array of assoc array dengan key name/email/role/dst (opsional).
 */
function usersImportCreateUploadFile(array $dataRows): UploadedFile
{
    $headers = ['name', 'email', 'role', 'phone', 'birth_date', 'gender', 'address', 'status'];

    $sheetRows = [$headers];
    foreach ($dataRows as $row) {
        $sheetRows[] = array_map(fn ($header) => $row[$header] ?? null, $headers);
    }

    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray($sheetRows, null, 'A1');

    $path = tempnam(sys_get_temp_dir(), 'users_import_').'.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($path);

    return new UploadedFile(
        $path,
        'users-import.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );
}

function usersImportCreateAdmin(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);
}

/*
|--------------------------------------------------------------------------
| Kontrak perilaku upsert (unit-level pada UsersImport::model)
|--------------------------------------------------------------------------
*/

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

    $import = new UsersImport;
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

    $import = new UsersImport;
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

    $import = new UsersImport;
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

    $import = new UsersImport;
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

    $import = new UsersImport;
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

    $import = new UsersImport;
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

    $import = new UsersImport;
    $preparedRow = $import->prepareForValidation($row);

    // Assert that the prepared row has normalized lowercase values that pass validation rules
    expect($preparedRow['role'])->toBe('mahasiswa');
    expect($preparedRow['gender'])->toBe('laki-laki');
    expect($preparedRow['status'])->toBe('active');

    // Make sure we can validate using rules()
    $validator = \Illuminate\Support\Facades\Validator::make($preparedRow, $import->rules());
    expect($validator->fails())->toBeFalse();
});

/*
|--------------------------------------------------------------------------
| End-to-end: HTTP import via route admin.users.import.store
|--------------------------------------------------------------------------
*/

test('import baris baru melalui HTTP men-insert user dan pesan menyebut jumlah baru/diperbarui/gagal', function () {
    $admin = usersImportCreateAdmin();

    $file = usersImportCreateUploadFile([
        ['name' => 'Siswa Satu', 'email' => 'siswa1@example.com', 'role' => 'siswa'],
        ['name' => 'Siswa Dua', 'email' => 'siswa2@example.com', 'role' => 'siswa'],
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => $file]);

    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'Data user berhasil diimpor! (2 baru, 0 diperbarui, 0 gagal)');

    expect(User::where('email', 'siswa1@example.com')->count())->toBe(1);
    expect(User::where('email', 'siswa2@example.com')->count())->toBe(1);
});

test('import ulang file yang sama bersifat idempoten: tanpa duplikat, semua diperbarui, 0 gagal', function () {
    $admin = usersImportCreateAdmin();

    $rows = [
        ['name' => 'Siswa Satu', 'email' => 'siswa1@example.com', 'role' => 'siswa', 'phone' => '081111111111'],
        ['name' => 'Siswa Dua', 'email' => 'siswa2@example.com', 'role' => 'siswa'],
    ];

    $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => usersImportCreateUploadFile($rows)]);
    expect(User::where('email', 'siswa1@example.com')->count())->toBe(1);

    // Import file yang SAMA persis
    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => usersImportCreateUploadFile($rows)]);

    $response->assertSessionHas('success', 'Data user berhasil diimpor! (0 baru, 2 diperbarui, 0 gagal)');

    // Tidak ada duplikat
    expect(User::where('email', 'siswa1@example.com')->count())->toBe(1);
    expect(User::where('email', 'siswa2@example.com')->count())->toBe(1);
});

test('import file dengan data terbaru meng-update (replace) user yang sudah ada', function () {
    $admin = usersImportCreateAdmin();

    $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => usersImportCreateUploadFile([
        ['name' => 'Siswa Satu', 'email' => 'siswa1@example.com', 'role' => 'siswa', 'phone' => '081111111111'],
    ])]);

    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => usersImportCreateUploadFile([
        ['name' => 'Siswa Satu Diperbarui', 'email' => 'siswa1@example.com', 'role' => 'siswa', 'phone' => '089999999999', 'gender' => 'laki-laki'],
    ])]);

    $response->assertSessionHas('success', 'Data user berhasil diimpor! (0 baru, 1 diperbarui, 0 gagal)');

    $user = User::where('email', 'siswa1@example.com')->first();
    expect($user->name)->toBe('Siswa Satu Diperbarui');
    expect($user->phone)->toBe('089999999999');
    expect($user->gender)->toBe('laki-laki');
    expect(User::count())->toBe(2); // admin + 1 siswa (tidak bertambah)
});

test('baris kotor (spasi di email, kapitalisasi, serial tanggal Excel) tetap diimpor alih-alih gagal', function () {
    $admin = usersImportCreateAdmin();

    $serialBirthDate = 45678; // serial number Excel
    $expectedBirthDate = ExcelDate::excelToDateTimeObject($serialBirthDate)->format('Y-m-d');

    $file = usersImportCreateUploadFile([
        ['name' => 'Siswa Spasi', 'email' => '  siswa.spasi@example.com ', 'role' => 'Siswa', 'birth_date' => $serialBirthDate],
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => $file]);

    $response->assertSessionHas('success', 'Data user berhasil diimpor! (1 baru, 0 diperbarui, 0 gagal)');

    $user = User::where('email', 'siswa.spasi@example.com')->first();
    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Siswa Spasi');
    expect($user->role)->toBe('siswa');
    expect($user->birth_date->format('Y-m-d'))->toBe($expectedBirthDate);
});

test('baris invalid tetap gagal dengan pesan error per baris yang jelas', function () {
    $admin = usersImportCreateAdmin();

    $file = usersImportCreateUploadFile([
        ['name' => 'Siswa Valid', 'email' => 'valid@example.com', 'role' => 'siswa'],
        ['name' => '', 'email' => 'tanpanama@example.com', 'role' => 'siswa'],        // nama kosong -> Baris 3
        ['name' => 'Siswa Role', 'email' => 'role@example.com', 'role' => 'guru bk'], // role tak dikenal -> Baris 4
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => $file]);

    // Partial success: 1 baris valid masuk, 2 baris invalid gagal
    $response->assertSessionHas('success', 'Data user berhasil diimpor! (1 baru, 0 diperbarui, 2 gagal) (cek log untuk detail)');

    $details = session('import_errors');
    expect($details)->not->toBeEmpty();

    $joined = implode(' | ', $details);
    expect($joined)->toContain('Baris 3');
    expect($joined)->toContain('name');
    expect($joined)->toContain('Nama wajib diisi');
    expect($joined)->toContain('Baris 4');
    expect($joined)->toContain('Role harus salah satu dari');

    expect(User::where('email', 'valid@example.com')->count())->toBe(1);
    expect(User::where('email', 'tanpanama@example.com')->count())->toBe(0);
    expect(User::where('email', 'role@example.com')->count())->toBe(0);
});

test('duplikat email dalam file yang sama tidak menyebabkan error/gagal massal dan tetap idempoten', function () {
    $admin = usersImportCreateAdmin();

    $file = usersImportCreateUploadFile([
        ['name' => 'Duplikat Pertama', 'email' => 'duplikat@example.com', 'role' => 'siswa'],
        ['name' => 'Duplikat Kedua', 'email' => 'duplikat@example.com', 'role' => 'siswa'],
        ['name' => 'User Lain', 'email' => 'lain@example.com', 'role' => 'siswa'],
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => $file]);

    // Baris duplikat kedua menjadi UPDATE baris pertama, bukan error batch;
    // baris ketiga tetap diinsert sehingga hasilnya 2 baru + 1 diperbarui
    $response->assertSessionHas('success', 'Data user berhasil diimpor! (2 baru, 1 diperbarui, 0 gagal)');

    expect(User::where('email', 'duplikat@example.com')->count())->toBe(1);
    expect(User::where('email', 'duplikat@example.com')->first()->name)->toBe('Duplikat Kedua');
    expect(User::where('email', 'lain@example.com')->count())->toBe(1);
});

test('detail kegagalan per baris ditulis ke log aplikasi (laravel.log)', function () {
    Log::spy();

    $admin = usersImportCreateAdmin();

    $file = usersImportCreateUploadFile([
        ['name' => 'Siswa Valid', 'email' => 'valid@example.com', 'role' => 'siswa'],
        ['name' => '', 'email' => 'tanpanama@example.com', 'role' => 'siswa'],
    ]);

    $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => $file]);

    Log::shouldHaveReceived('warning')->withArgs(function ($message, $context = []) {
        return is_string($message)
            && str_contains($message, 'gagal diimpor')
            && isset($context['details'])
            && collect($context['details'])->contains(fn ($detail) => str_contains((string) $detail, 'Baris 3'));
    })->once();
});
