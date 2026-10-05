<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

/**
 * Import user dengan perilaku UPSERT idempoten.
 *
 * Unique key urutan pencarian: id (bila ada di file) -> email -> username.
 * - Record dengan key yang sama sudah ada di database -> data di-replace/update.
 * - Belum ada -> insert seperti biasa (password default bila kolom kosong).
 * - Baris invalid (field wajib kosong/format salah) tetap gagal dengan pesan per baris.
 *
 * Catatan: sengaja TANPA WithBatchInserts agar setiap baris disimpan per-row
 * (singleFlush). Mass-insert per chunk membuat SATU baris bermasalah menjatuhkan
 * seluruh chunk (hingga 100 baris) sekaligus, dan model events (generasi username)
 * tidak ikut berjalan.
 */
class UsersImport implements SkipsEmptyRows, SkipsOnError, SkipsOnFailure, ToModel, WithChunkReading, WithHeadingRow, WithValidation
{
    use Importable, SkipsErrors, SkipsFailures;

    protected int $createdCount = 0;

    protected int $updatedCount = 0;

    /**
     * Nomor baris asli di sheet (1-based, termasuk baris heading) untuk baris
     * yang sedang diproses — dipakai agar pesan error menyebut nomor baris.
     */
    protected int $currentRowNumber = 0;

    /**
     * Normalisasi data baris SEBELUM validasi oleh Maatwebsite Excel.
     *
     * Menangani masalah yang menyebabkan kegagalan massal pada import produksi:
     * - spasi/whitespace di awal-takhir field (terutama email) yang membuat rule `email` gagal;
     * - kapitalisasi berlebih pada email/role/gender/status;
     * - birth_date berupa serial number Excel (angka) atau objek DateTime;
     * - karakter BOM hasil export CSV.
     *
     * @param  int  $rowNumber  nomor baris asli di sheet (untuk pesan error per baris)
     */
    public function prepareForValidation(array $row, int $rowNumber = 0): array
    {
        if ($rowNumber > 0) {
            $this->currentRowNumber = $rowNumber;
        }

        // Bersihkan karakter BOM (umum pada CSV hasil export Excel)
        foreach ($row as $key => $value) {
            if (is_string($value)) {
                $row[$key] = preg_replace('/^\xEF\xBB\xBF/', '', $value);
            }
        }

        // Trim semua kolom teks; string kosong -> null (agar lolos rule nullable/required yang benar)
        $stringFields = ['id', 'name', 'email', 'password', 'role', 'phone', 'birth_date', 'gender', 'address', 'status', 'username'];
        foreach ($stringFields as $field) {
            if (isset($row[$field]) && is_string($row[$field])) {
                $trimmed = trim($row[$field]);
                $row[$field] = $trimmed === '' ? null : $trimmed;
            }
        }

        // Email dinormalkan ke huruf kecil agar lookup upsert konsisten
        if (isset($row['email']) && is_string($row['email'])) {
            $row['email'] = strtolower($row['email']);
        }

        // birth_date: serial Excel / DateTime object / string -> Y-m-d
        if (array_key_exists('birth_date', $row)) {
            $row['birth_date'] = $this->normalizeBirthDate($row['birth_date']);
        }

        // Konversi scalar (mis. Excel numeric) menjadi string agar bisa dinormalkan
        foreach (['role', 'gender', 'status'] as $field) {
            if (isset($row[$field]) && ! is_string($row[$field]) && ! is_bool($row[$field])) {
                $row[$field] = (string) $row[$field];
            } elseif (isset($row[$field]) && is_bool($row[$field])) {
                $row[$field] = $row[$field] ? '1' : '0';
            }
        }

        // Normalisasi role agar lolos validasi rules
        if (isset($row['role']) && is_string($row['role'])) {
            $roleMap = [
                'admin' => 'admin',
                'administrator' => 'admin',
                'guru' => 'guru',
                'teacher' => 'guru',
                'siswa' => 'siswa',
                'student' => 'siswa',
                'dosen' => 'dosen',
                'mahasiswa' => 'mahasiswa',
            ];
            $roleLower = strtolower($row['role']);
            // Nilai tak dikenal dibiarkan apa adanya: validasi menolak dengan pesan jelas per baris
            if (isset($roleMap[$roleLower])) {
                $row['role'] = $roleMap[$roleLower];
            }
        }

        // Normalisasi gender agar lolos validasi rules
        if (isset($row['gender']) && is_string($row['gender'])) {
            $genderMap = [
                'laki-laki' => 'laki-laki',
                'laki laki' => 'laki-laki',
                'male' => 'laki-laki',
                'm' => 'laki-laki',
                'pria' => 'laki-laki',
                'perempuan' => 'perempuan',
                'female' => 'perempuan',
                'f' => 'perempuan',
                'wanita' => 'perempuan',
            ];
            $genderLower = strtolower($row['gender']);
            if (isset($genderMap[$genderLower])) {
                $row['gender'] = $genderMap[$genderLower];
            }
        }

        // Normalisasi status agar lolos validasi rules
        if (isset($row['status']) && is_string($row['status'])) {
            $statusMap = [
                'active' => 'active',
                'aktif' => 'active',
                '1' => 'active',
                'inactive' => 'inactive',
                'tidak aktif' => 'inactive',
                '0' => 'inactive',
            ];
            $statusLower = strtolower($row['status']);
            if (isset($statusMap[$statusLower])) {
                $row['status'] = $statusMap[$statusLower];
            }
        }

        return $row;
    }

    /**
     * Proses per baris: UPSERT by id -> email -> username.
     *
     * @return \App\Models\User|null null bila record existing di-update di tempat
     */
    public function model(array $row): ?User
    {
        // Default password dari config
        $defaultPassword = config('app.default_user_password', 'LMS2024@Pass');

        $email = null;
        if (isset($row['email']) && is_string($row['email'])) {
            $email = strtolower(trim($row['email']));
            if ($email === '') {
                $email = null;
            }
        }

        // Cari user yang sudah ada: ID -> email -> username (unique key upsert)
        $user = null;
        if (! empty($row['id']) && is_numeric($row['id'])) {
            $user = User::find((int) $row['id']);
        }
        if (! $user && $email !== null) {
            $user = User::where('email', $email)->first();
        }
        if (! $user && ! empty($row['username']) && is_string($row['username'])) {
            $user = User::where('username', trim($row['username']))->first();
        }

        if ($user) {
            // Email existing yang sudah dipakai user lain -> tolak dengan pesan jelas
            if ($email !== null && $email !== strtolower($user->email)) {
                $exists = User::where('email', $email)->where('id', '!=', $user->id)->exists();
                if ($exists) {
                    throw new \Exception("Email '{$email}' sudah digunakan oleh pengguna lain.");
                }
                $user->email = $email;
            }

            $user->name = $row['name'] ?? $user->name;

            // Password HANYA diganti bila kolom password diisi pada file
            if (isset($row['password']) && $row['password'] !== '' && $row['password'] !== null) {
                $user->password = Hash::make($row['password']);
            }

            $user->role = $this->mapRole($row['role'] ?? $user->role);
            $user->phone = $row['phone'] ?? null;
            $user->birth_date = $this->parseDate($row['birth_date'] ?? null);
            $user->gender = $this->mapGender($row['gender'] ?? null);
            $user->address = $row['address'] ?? null;
            $user->is_active = $this->mapStatus($row['status'] ?? ($user->is_active ? 'active' : 'inactive'));

            $user->save();

            $this->updatedCount++;

            return null; // Return null agar tidak dibuat record baru oleh Maatwebsite Excel
        }

        // Tentukan password untuk user baru
        $password = $defaultPassword;
        if (isset($row['password']) && $row['password'] !== '' && $row['password'] !== null) {
            $password = $row['password'];
        }

        $this->createdCount++;

        return new User([
            'name' => $row['name'] ?? null,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => $this->mapRole($row['role'] ?? 'siswa'),
            'phone' => $row['phone'] ?? null,
            'birth_date' => $this->parseDate($row['birth_date'] ?? null),
            'gender' => $this->mapGender($row['gender'] ?? null),
            'address' => $row['address'] ?? null,
            'is_active' => $this->mapStatus($row['status'] ?? 'active'),
            'email_verified_at' => now(), // Auto verified
        ]);
    }

    /**
     * Tangani error penyimpanan per baris dengan menyebut nomor baris sheet.
     */
    public function onError(\Throwable $e): void
    {
        $prefix = $this->currentRowNumber > 0 ? "Baris {$this->currentRowNumber}: " : '';
        $this->errors[] = new \RuntimeException($prefix.$e->getMessage(), (int) $e->getCode(), $e);
    }

    /**
     * Rules validasi per baris. Baris yang gagal validasi dilewati (SkipsOnFailure)
     * dengan pesan per baris yang bisa dibaca.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'nullable|string|in:admin,guru,siswa,dosen,mahasiswa',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:laki-laki,perempuan',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function customValidationMessages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'name.string' => 'Nama harus berupa teks',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'role.in' => 'Role harus salah satu dari: admin, guru, siswa, dosen, mahasiswa',
            'phone.max' => 'Nomor telepon maksimal 20 karakter',
            'birth_date.date' => 'Format tanggal lahir tidak valid (gunakan YYYY-MM-DD)',
            'gender.in' => 'Jenis kelamin harus salah satu dari: laki-laki, perempuan',
            'address.max' => 'Alamat maksimal 500 karakter',
            'status.in' => 'Status harus salah satu dari: active, inactive',
        ];
    }

    /**
     * Konversi berbagai bentuk tanggal lahir menjadi Y-m-d.
     * Serial number Excel dan objek DateTime dikonversi; string yang tidak
     * terbaca dibiarkan apa adanya sehingga validasi menolak barisnya dengan pesan jelas.
     */
    private function normalizeBirthDate(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        // Serial number Excel (contoh: 45678)
        if (is_numeric($value)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        if (is_string($value)) {
            try {
                return Carbon::parse($value)->format('Y-m-d');
            } catch (\Exception $e) {
                foreach (['d/m/Y', 'd-m-Y', 'd.m.Y'] as $format) {
                    try {
                        return Carbon::createFromFormat($format, $value)->format('Y-m-d');
                    } catch (\Exception $ignored) {
                        // lanjut ke format berikutnya
                    }
                }

                return $value;
            }
        }

        return $value;
    }

    /**
     * Map role from Excel to database value
     */
    private function mapRole($role)
    {
        if (! is_string($role) || $role === '') {
            return 'siswa';
        }

        $roleMap = [
            'admin' => 'admin',
            'administrator' => 'admin',
            'guru' => 'guru',
            'teacher' => 'guru',
            'siswa' => 'siswa',
            'student' => 'siswa',
            'dosen' => 'dosen',
            'mahasiswa' => 'mahasiswa',
        ];

        return $roleMap[strtolower($role)] ?? 'siswa';
    }

    /**
     * Map gender from Excel to database value
     */
    private function mapGender($gender)
    {
        if (! $gender || ! is_string($gender)) {
            return null;
        }

        $genderMap = [
            'laki-laki' => 'laki-laki',
            'laki laki' => 'laki-laki',
            'male' => 'laki-laki',
            'm' => 'laki-laki',
            'pria' => 'laki-laki',
            'perempuan' => 'perempuan',
            'female' => 'perempuan',
            'f' => 'perempuan',
            'wanita' => 'perempuan',
        ];

        return $genderMap[strtolower($gender)] ?? null;
    }

    /**
     * Map status from Excel to database value
     */
    private function mapStatus($status)
    {
        if (! $status || ! is_string($status)) {
            return true;
        }

        $statusMap = [
            'active' => true,
            'aktif' => true,
            '1' => true,
            'inactive' => false,
            'tidak aktif' => false,
            '0' => false,
        ];

        return $statusMap[strtolower($status)] ?? true;
    }

    /**
     * Parse date from various formats
     */
    private function parseDate($date)
    {
        if (! $date) {
            return null;
        }

        if ($date instanceof \DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get import statistics.
     *
     * @return array<string, mixed>
     */
    public function getStats(): array
    {
        $failureMessages = $this->failures()->map(function ($failure) {
            $field = $failure->attribute();
            $parts = array_map('strval', $failure->errors());

            return "Baris {$failure->row()} ({$field}) — ".implode('; ', $parts);
        })->values()->all();

        $errorMessages = $this->errors()->map(function ($error) {
            return Str::limit($error->getMessage(), 300);
        })->values()->all();

        return [
            'created' => $this->createdCount,
            'updated' => $this->updatedCount,
            'imported' => $this->createdCount + $this->updatedCount,
            'skipped' => $this->failures()->count() + $this->errors()->count(),
            'failure_messages' => $failureMessages,
            'error_messages' => $errorMessages,
        ];
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
