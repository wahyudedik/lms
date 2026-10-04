<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\MaterialAttendance;
use App\Models\User;

class MaterialAttendancePolicy
{
    /**
     * Determine if the user can manage attendance for the given material.
     *
     * Admin boleh semua; guru/dosen hanya kursus yang diajar.
     */
    public function manage(User $user, Material $material): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (! $material->relationLoaded('course')) {
            $material->load('course');
        }

        return ($user->isGuru() || $user->isDosen())
            && $material->course
            && $material->course->instructor_id === $user->id;
    }

    /**
     * Determine if the user can view any attendance records.
     */
    public function viewAny(User $user): bool
    {
        // Semua role terautentikasi dapat mengakses fitur absensi
        // (admin/guru/dosen mengelola, siswa/mahasiswa melihat data sendiri).
        return true;
    }

    /**
     * Determine if the user can view the attendance record.
     */
    public function view(User $user, MaterialAttendance $attendance): bool
    {
        // Siswa melihat data absensi miliknya sendiri
        if ($attendance->user_id === $user->id) {
            return true;
        }

        if ($attendance->material) {
            return $this->manage($user, $attendance->material);
        }

        return false;
    }
}
