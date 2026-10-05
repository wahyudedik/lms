<?php

namespace Tests\Feature\Admin;

use App\Jobs\ImportUsersJob;
use App\Models\User;
use App\Models\UserImport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
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
 */
function userQueueImportCreateUploadFile(array $dataRows): UploadedFile
{
    $headers = ['name', 'email', 'role', 'phone', 'birth_date', 'gender', 'address', 'status'];

    $sheetRows = [$headers];
    foreach ($dataRows as $row) {
        $sheetRows[] = array_map(fn ($header) => $row[$header] ?? null, $headers);
    }

    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray($sheetRows, null, 'A1');

    $path = tempnam(sys_get_temp_dir(), 'user_queue_import_').'.xlsx';
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

/**
 * Membuat konten XLSX sebagai string (untuk disimpan langsung ke storage fake).
 */
function userQueueImportXlsxContent(array $dataRows): string
{
    $headers = ['name', 'email', 'role', 'phone', 'birth_date', 'gender', 'address', 'status'];

    $sheetRows = [$headers];
    foreach ($dataRows as $row) {
        $sheetRows[] = array_map(fn ($header) => $row[$header] ?? null, $headers);
    }

    $spreadsheet = new Spreadsheet;
    $spreadsheet->getActiveSheet()->fromArray($sheetRows, null, 'A1');

    $path = tempnam(sys_get_temp_dir(), 'user_queue_import_').'.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($path);

    $content = file_get_contents($path);
    unlink($path);

    return $content;
}

function userQueueImportCreateAdmin(): User
{
    return User::factory()->create([
        'role' => 'admin',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);
}

/*
|--------------------------------------------------------------------------
| Kontrak alur queue-based import
|--------------------------------------------------------------------------
*/

test('post import valid membuat record user_imports pending, mendispatch job, dan tidak membuat user', function () {
    Queue::fake();
    Storage::fake('local');

    $admin = userQueueImportCreateAdmin();

    $file = userQueueImportCreateUploadFile([
        ['name' => 'Siswa Queue', 'email' => 'queue@example.com', 'role' => 'siswa'],
    ]);

    $response = $this->actingAs($admin)->post(route('admin.users.import.store'), ['file' => $file]);

    // Record dibuat dengan status pending
    $record = UserImport::where('user_id', $admin->id)->latest()->first();
    expect($record)->not->toBeNull();
    expect($record->status)->toBe('pending');
    expect($record->file_name)->toBe('users-import.xlsx');

    // Job terdispatch (tidak dieksekusi — Queue::fake)
    Queue::assertPushed(
        ImportUsersJob::class,
        fn (ImportUsersJob $job) => $job->userImport->id === $record->id
    );

    // User BELUM terbuat di DB (diproses oleh worker)
    expect(User::where('email', 'queue@example.com')->count())->toBe(0);

    // Response redirect + flash pesan background
    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'File diterima, sedang diproses di background. Muat ulang halaman untuk melihat progres.');
});

test('menjalankan job langsung membuat user dan menandai record completed dengan counts dan finished_at', function () {
    Storage::fake('local');

    $admin = userQueueImportCreateAdmin();

    Storage::disk('local')->put('imports/direct-run.xlsx', userQueueImportXlsxContent([
        ['name' => 'Siswa Direct', 'email' => 'direct1@example.com', 'role' => 'siswa'],
        ['name' => 'Guru Direct', 'email' => 'direct2@example.com', 'role' => 'guru'],
    ]));

    $record = UserImport::create([
        'user_id' => $admin->id,
        'file_name' => 'direct-run.xlsx',
        'file_path' => 'imports/direct-run.xlsx',
        'status' => UserImport::STATUS_PENDING,
    ]);

    (new ImportUsersJob($record))->handle();

    $record->refresh();

    expect($record->status)->toBe('completed');
    expect($record->created_count)->toBe(2);
    expect($record->updated_count)->toBe(0);
    expect($record->failed_count)->toBe(0);
    expect($record->total_rows)->toBe(2);
    expect($record->finished_at)->not->toBeNull();

    expect(User::where('email', 'direct1@example.com')->count())->toBe(1);
    expect(User::where('email', 'direct2@example.com')->count())->toBe(1);

    // File sementara dihapus setelah selesai
    expect(Storage::disk('local')->exists('imports/direct-run.xlsx'))->toBeFalse();
});

test('menjalankan job dua kali terhadap data sama bersifat idempoten: kedua kali updated_count terisi', function () {
    Storage::fake('local');

    $admin = userQueueImportCreateAdmin();

    $rows = [
        ['name' => 'Siswa Idem', 'email' => 'idem1@example.com', 'role' => 'siswa'],
        ['name' => 'Siswa Idem Dua', 'email' => 'idem2@example.com', 'role' => 'siswa'],
    ];

    // Run pertama
    Storage::disk('local')->put('imports/idem-1.xlsx', userQueueImportXlsxContent($rows));
    $record1 = UserImport::create([
        'user_id' => $admin->id,
        'file_name' => 'idem-1.xlsx',
        'file_path' => 'imports/idem-1.xlsx',
        'status' => UserImport::STATUS_PENDING,
    ]);
    (new ImportUsersJob($record1))->handle();

    $record1->refresh();
    expect($record1->status)->toBe('completed');
    expect($record1->created_count)->toBe(2);
    expect(User::where('email', 'idem1@example.com')->count())->toBe(1);

    // Run kedua — data sama
    Storage::disk('local')->put('imports/idem-2.xlsx', userQueueImportXlsxContent($rows));
    $record2 = UserImport::create([
        'user_id' => $admin->id,
        'file_name' => 'idem-2.xlsx',
        'file_path' => 'imports/idem-2.xlsx',
        'status' => UserImport::STATUS_PENDING,
    ]);
    (new ImportUsersJob($record2))->handle();

    $record2->refresh();
    expect($record2->status)->toBe('completed');
    expect($record2->created_count)->toBe(0);
    expect($record2->updated_count)->toBe(2);
    expect($record2->failed_count)->toBe(0);

    // Jumlah user tidak berubah (tanpa duplikat)
    expect(User::where('email', 'idem1@example.com')->count())->toBe(1);
    expect(User::where('email', 'idem2@example.com')->count())->toBe(1);
});

test('file rusak membuat record failed dengan pesan error dan file sementara dihapus', function () {
    Storage::fake('local');

    $admin = userQueueImportCreateAdmin();

    // Bukan xlsx valid — reader akan melempar exception
    Storage::disk('local')->put('imports/broken.xlsx', 'ini bukan file xlsx yang valid >>>');

    $record = UserImport::create([
        'user_id' => $admin->id,
        'file_name' => 'broken.xlsx',
        'file_path' => 'imports/broken.xlsx',
        'status' => UserImport::STATUS_PENDING,
    ]);

    (new ImportUsersJob($record))->handle();

    $record->refresh();

    expect($record->status)->toBe('failed');
    expect($record->errors)->not->toBeNull();
    expect($record->errors[0])->toContain('Import gagal');
    expect($record->finished_at)->not->toBeNull();

    // File sementara dihapus meski gagal
    expect(Storage::disk('local')->exists('imports/broken.xlsx'))->toBeFalse();
});

test('user non-admin ditolak mengakses route import (403)', function () {
    Storage::fake('local');

    $siswa = User::factory()->create([
        'role' => 'siswa',
        'email_verified_at' => now(),
        'is_active' => true,
    ]);

    $file = userQueueImportCreateUploadFile([
        ['name' => 'Siswa', 'email' => 'blocked@example.com', 'role' => 'siswa'],
    ]);

    $response = $this->actingAs($siswa)->post(route('admin.users.import.store'), ['file' => $file]);

    $response->assertForbidden();

    expect(UserImport::count())->toBe(0);
    expect(User::where('email', 'blocked@example.com')->count())->toBe(0);
});

test('method handle job menandai processing sebelum menyelesaikan impor', function () {
    Storage::fake('local');

    $admin = userQueueImportCreateAdmin();

    Storage::disk('local')->put('imports/status-track.xlsx', userQueueImportXlsxContent([
        ['name' => 'Siswa Status', 'email' => 'status@example.com', 'role' => 'siswa'],
    ]));

    $record = UserImport::create([
        'user_id' => $admin->id,
        'file_name' => 'status-track.xlsx',
        'file_path' => 'imports/status-track.xlsx',
        'status' => UserImport::STATUS_PENDING,
    ]);

    $observed = [];
    $record->updating(function (UserImport $model) use (&$observed) {
        $observed[] = $model->status;
    });

    (new ImportUsersJob($record))->handle();

    expect($observed)->toContain(UserImport::STATUS_PROCESSING);
    expect($record->refresh()->status)->toBe(UserImport::STATUS_COMPLETED);
});
