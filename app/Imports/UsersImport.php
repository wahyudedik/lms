<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    protected int $importedCount = 0;

    /**
     * Konversi empty string dari Excel menjadi null untuk field opsional.
     * Dipanggil sebelum validasi oleh Maatwebsite Excel.
     *
     * @param array $row
     * @return array
     */
    public function prepareForValidation(array $row): array
    {
        $optionalFields = ['phone', 'birth_date', 'gender', 'address', 'status'];

        foreach ($optionalFields as $field) {
            if (isset($row[$field]) && is_string($row[$field]) && trim($row[$field]) === '') {
                $row[$field] = null;
            }
        }

        return $row;
    }

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Default password dari config
        $defaultPassword = config('app.default_user_password', 'LMS2024@Pass');

        $this->importedCount++;

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($defaultPassword),
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
     * Validasi harus menggunakan WithValidation concern atau custom handling.
     * Karena kita sudah implement prepareForValidation, kita handle validasi manual
     * untuk menghindari konflik empty string.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'nullable|string|in:admin,guru,siswa,dosen,mahasiswa',
            'phone' => 'nullable|string|max:20',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|in:laki-laki,perempuan',
            'address' => 'nullable|string|max:500',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }

    /**
     * @return array
     */
    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Email harus valid',
            'email.unique' => 'Email sudah terdaftar di sistem',
            'role.in' => 'Role harus salah satu dari: admin, guru, siswa, dosen, mahasiswa',
            'gender.in' => 'Jenis kelamin harus salah satu dari: laki-laki, perempuan',
            'status.in' => 'Status harus salah satu dari: active, inactive',
        ];
    }

    /**
     * Map role from Excel to database value
     */
    private function mapRole($role)
    {
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
        if (!$gender) return null;

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
        if (!$status) return true;

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
        if (!$date) return null;

        try {
            return \Carbon\Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get import statistics
     */
    public function getStats(): array
    {
        return [
            'imported' => $this->importedCount,
            'skipped' => $this->failures()->count() + $this->errors()->count(),
            'failure_messages' => $this->failures()->map(function ($failure) {
                return "Baris {$failure->row()}: " . implode(', ', $failure->errors());
            })->toArray(),
            'error_messages' => $this->errors()->map(function ($error) {
                return $error->getMessage();
            })->toArray(),
        ];
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 100;
    }
}
