<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolClass;
use App\Models\User;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::query()->withCount('activeCheatingIncidents');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->has('role') && $request->role) {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        $classes = SchoolClass::query()
            ->orderByDesc('is_general')
            ->orderBy('education_level')
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users', 'classes'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $classes = SchoolClass::query()
            ->orderByDesc('is_general')
            ->orderBy('education_level')
            ->orderBy('name')
            ->get();

        return view('admin.users.create', compact('classes'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:admin,guru,siswa,dosen,mahasiswa'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'address' => ['nullable', 'string', 'max:500'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'is_active' => ['boolean'],
        ]);

        $schoolClassId = $validated['school_class_id'] ?? null;
        if (in_array($validated['role'], ['siswa', 'mahasiswa']) && !$schoolClassId) {
            $schoolClassId = SchoolClass::general()->id;
        }

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'school_class_id' => $schoolClassId,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $recentIncidents = $user->cheatingIncidents()->latest()->limit(5)->get();
        $activeIncidentCount = $user->activeCheatingIncidents()->count();

        return view('admin.users.show', compact('user', 'recentIncidents', 'activeIncidentCount'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $classes = SchoolClass::query()
            ->orderByDesc('is_general')
            ->orderBy('education_level')
            ->orderBy('name')
            ->get();

        return view('admin.users.edit', compact('user', 'classes'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,guru,siswa,dosen,mahasiswa'],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'address' => ['nullable', 'string', 'max:500'],
            'school_class_id' => ['nullable', 'exists:school_classes,id'],
            'is_active' => ['boolean'],
        ]);

        $schoolClassId = $validated['school_class_id'] ?? null;
        if (in_array($validated['role'], ['siswa', 'mahasiswa']) && !$schoolClassId) {
            $schoolClassId = SchoolClass::general()->id;
        }

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'phone' => $validated['phone'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'address' => $validated['address'] ?? null,
            'school_class_id' => $schoolClassId,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('admin.users.edit', $user)
            ->with('success', 'User password updated successfully.');
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user)
    {
        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('admin.users.index')
            ->with('success', "User {$status} successfully.");
    }

    /**
     * Reset user login block (admin action)
     */
    public function resetLogin(Request $request, User $user)
    {
        if (!$user->is_login_blocked) {
            return back()->with('info', 'User login is not blocked.');
        }

        $user->resetLoginBlock($request->user());

        return back()->with('success', 'User login access has been reset.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        // Delete profile photo if exists
        $user->deleteProfilePhoto();

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Remove multiple users in bulk
     */
    public function destroyBulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:users,id'],
        ]);

        $ids = $validated['ids'];

        // Prevent admin from deleting themselves
        if (in_array(auth()->id(), $ids)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Delete users and their photos
        $users = User::whereIn('id', $ids)->get();
        $count = $users->count();

        /** @var \App\Models\User $user */
        foreach ($users as $user) {
            $user->deleteProfilePhoto();
            $user->delete();
        }

        return redirect()->route('admin.users.index')
            ->with('success', "{$count} pengguna berhasil dihapus secara massal.");
    }

    /**
     * Update multiple users' class in bulk
     */
    public function updateClassBulk(Request $request)
    {
        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['required', 'integer', 'exists:users,id'],
            'school_class_id' => ['required', 'integer', 'exists:school_classes,id'],
        ]);

        $ids = $validated['ids'];
        $classId = $validated['school_class_id'];

        // Exclude the current admin from class update to prevent unintended issues
        $ids = array_diff($ids, [auth()->id()]);

        if (empty($ids)) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Tidak ada pengguna valid yang dipilih.');
        }

        // Update classes
        $count = User::whereIn('id', $ids)->update(['school_class_id' => $classId]);

        return redirect()->route('admin.users.index')
            ->with('success', "Kelas untuk {$count} pengguna berhasil diperbarui.");
    }

    /**
     * Export users to Excel with their passwords
     */
    public function export(Request $request)
    {
        $filters = $request->only(['search', 'role', 'status']);

        $filename = 'users_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new UsersExport($filters), $filename);
    }

    /**
     * Show import form
     */
    public function importForm()
    {
        return view('admin.users.import');
    }

    /**
     * Import users from Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240', // 10MB max
        ]);

        try {
            $import = new UsersImport();
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();

            if ($stats['imported'] > 0) {
                $message = "{$stats['imported']} pengguna berhasil diimport.";

                if ($stats['skipped'] > 0) {
                    $message .= " {$stats['skipped']} baris dilewati.";
                    // Store skipped details in session for potential flash display
                    session(['import_errors' => array_merge(
                        $stats['failure_messages'] ?? [],
                        $stats['error_messages'] ?? []
                    )]);
                }

                return redirect()->route('admin.users.index')
                    ->with('success', $message);
            } else {
                $errorMessages = array_merge(
                    $stats['failure_messages'] ?? [],
                    $stats['error_messages'] ?? []
                );

                $errorMsg = 'Tidak ada pengguna yang diimport.';
                if (!empty($errorMessages)) {
                    $errorMsg .= ' Error: ' . implode(' | ', array_slice($errorMessages, 0, 5));
                }

                return redirect()->back()->with('error', $errorMsg);
            }
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Import gagal: ' . $e->getMessage());
        }
    }

    /**
     * Download sample import template
     */
    public function downloadTemplate()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // === Header Row ===
        $headers = [
            'name',
            'email',
            'role',
            'phone',
            'birth_date',
            'gender',
            'address',
            'status',
        ];

        $sheet->fromArray($headers, null, 'A1');

        // === Panduan Format di Baris 2 (sebagai referensi, akan dihapus setelah diisi) ===
        $guideData = [
            [
                'Nama Lengkap',
                'email@contoh.com',
                'admin/guru/siswa/dosen/mahasiswa',
                '081234567890',
                'YYYY-MM-DD',
                'laki-laki/perempuan',
                'Alamat lengkap',
                'active/inactive',
            ],
        ];

        $sheet->fromArray($guideData, null, 'A2');

        // === Styling ===
        // Header style - bold dengan background biru
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4'],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A1:H1')->applyFromArray($headerStyle);

        // Guide row style - abu-abu muda, italic
        $guideStyle = [
            'font' => [
                'italic' => true,
                'color' => ['rgb' => '808080'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F2F2F2'],
            ],
        ];
        $sheet->getStyle('A2:H2')->applyFromArray($guideStyle);

        // Border untuk header dan guide
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'D9D9D9'],
                ],
            ],
        ];
        $sheet->getStyle('A1:H2')->applyFromArray($borderStyle);

        // Auto size columns
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Add notes di bawah guide
        $sheet->setCellValue('A4', 'Catatan:');
        $sheet->getStyle('A4')->getFont()->setBold(true)->setSize(11);
        $sheet->setCellValue('A5', '1. Kolom wajib diisi: name, email, role');
        $sheet->setCellValue('A6', '2. Kolom opsional: phone, birth_date, gender, address, status (boleh dikosongkan)');
        $sheet->setCellValue('A7', '3. Role yang didukung: admin, guru, siswa, dosen, mahasiswa');
        $sheet->setCellValue('A8', '4. Gender yang didukung: laki-laki, perempuan');
        $sheet->setCellValue('A9', '5. Status yang didukung: active, inactive');
        $sheet->setCellValue('A10', '6. Format tanggal: YYYY-MM-DD (contoh: 2000-01-01)');
        $sheet->setCellValue('A11', '7. Semua user akan mendapat password default: LMS2024@Pass');
        $sheet->setCellValue('A12', '8. Baris abu-abu (baris 2) adalah panduan format, silakan dihapus sebelum diisi');
        $sheet->getStyle('A5:A12')->getFont()->setSize(10);

        // Save and download
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $filename = 'template_import_pengguna.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $filename);
        $writer->save($temp_file);

        return response()->download($temp_file, $filename)->deleteFileAfterSend(true);
    }
}
