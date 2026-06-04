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
        $optionalFields = ['id', 'password', 'phone', 'birth_date', 'gender', 'address', 'status'];

        foreach ($optionalFields as $field) {
            if (isset($row[$field]) && is_string($row[$field]) && trim($row[$field]) === '') {
                $row[$field] = null;
            }
        }

        // Normalisasi role agar lolos validasi rules
        if (isset($row['role']) && is_string($row['role'])) {
            $roleLower = strtolower(trim($row['role']));
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
            if (isset($roleMap[$roleLower])) {
                $row['role'] = $roleMap[$roleLower];
            }
        }

        // Normalisasi gender agar lolos validasi rules
        if (isset($row['gender']) && is_string($row['gender'])) {
            $genderLower = strtolower(trim($row['gender']));
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
            if (isset($genderMap[$genderLower])) {
                $row['gender'] = $genderMap[$genderLower];
            }
        }

        // Normalisasi status agar lolos validasi rules
        if (isset($row['status']) && is_string($row['status'])) {
            $statusLower = strtolower(trim($row['status']));
            $statusMap = [
                'active' => 'active',
                'aktif' => 'active',
                '1' => 'active',
                'inactive' => 'inactive',
                'tidak aktif' => 'inactive',
                '0' => 'inactive',
            ];
            if (isset($statusMap[$statusLower])) {
                $row['status'] = $statusMap[$statusLower];
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

        // Cari user yang sudah ada berdasarkan ID atau Email
        $user = null;
        if (!empty($row['id'])) {
            $user = User::find($row['id']);
        }
        if (!$user && !empty($row['email'])) {
            $user = User::where('email', $row['email'])->first();
        }

        if ($user) {
            // Validasi email unik jika email diubah
            if (!empty($row['email']) && $row['email'] !== $user->email) {
                $exists = User::where('email', $row['email'])->where('id', '!=', $user->id)->exists();
                if ($exists) {
                    throw new \Exception("Email '{$row['email']}' sudah digunakan oleh pengguna lain.");
                }
                $user->email = $row['email'];
            }

            $user->name = $row['name'];
            
            // Update password jika diisi
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

            return null; // Return null agar tidak dibuat record baru oleh Maatwebsite Excel
        }

        // Tentukan password untuk user baru
        $password = $defaultPassword;
        if (isset($row['password']) && $row['password'] !== '' && $row['password'] !== null) {
            $password = $row['password'];
        }

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
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
