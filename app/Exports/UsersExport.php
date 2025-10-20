<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UsersExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = User::query();

        // Apply filters
        if (isset($this->filters['search']) && $this->filters['search']) {
            $search = $this->filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (isset($this->filters['role']) && $this->filters['role']) {
            $query->where('role', $this->filters['role']);
        }

        if (isset($this->filters['status']) && $this->filters['status'] !== '') {
            $query->where('is_active', $this->filters['status']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Password',
            'Role',
            'Phone',
            'Birth Date',
            'Gender',
            'Address',
            'Status',
            'Email Verified',
            'Created At',
            'Updated At',
        ];
    }

    /**
     * @param User $user
     * @return array
     */
    public function map($user): array
    {
        // Default password untuk semua user baru
        $defaultPassword = 'LMS2024@Pass';

        return [
            $user->id,
            $user->name,
            $user->email,
            $defaultPassword, // Default password yang sama untuk semua user
            $user->role_display,
            $user->phone,
            $user->birth_date ? $user->birth_date->format('Y-m-d') : '',
            $user->gender ? ucfirst($user->gender) : '',
            $user->address,
            $user->is_active ? 'Active' : 'Inactive',
            $user->email_verified_at ? 'Yes' : 'No',
            $user->created_at->format('Y-m-d H:i:s'),
            $user->updated_at->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
        ];
    }

    /**
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 10,  // ID
            'B' => 25,  // Name
            'C' => 30,  // Email
            'D' => 20,  // Password
            'E' => 15,  // Role
            'F' => 20,  // Phone
            'G' => 15,  // Birth Date
            'H' => 12,  // Gender
            'I' => 40,  // Address
            'J' => 12,  // Status
            'K' => 15,  // Email Verified
            'L' => 20,  // Created At
            'M' => 20,  // Updated At
        ];
    }
}
