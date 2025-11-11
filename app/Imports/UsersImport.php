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

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Default password dari config
        $defaultPassword = config('app.default_user_password');

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
     * @return array
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role' => 'nullable|string|in:admin,guru,siswa',
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
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email address',
            'email.unique' => 'Email already exists',
            'role.in' => 'Role must be one of: admin, guru, siswa',
            'gender.in' => 'Gender must be one of: laki-laki, perempuan',
            'status.in' => 'Status must be one of: active, inactive',
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
            'male' => 'laki-laki',
            'm' => 'laki-laki',
            'perempuan' => 'perempuan',
            'female' => 'perempuan',
            'f' => 'perempuan',
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
