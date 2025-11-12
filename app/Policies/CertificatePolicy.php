<?php

namespace App\Policies;

use App\Models\Certificate;
use App\Models\User;

class CertificatePolicy
{
    /**
     * Determine if the user can view any certificates.
     */
    public function viewAny(User $user): bool
    {
        // All authenticated users can view certificates list
        return true;
    }

    /**
     * Determine if the user can view the certificate.
     */
    public function view(User $user, Certificate $certificate): bool
    {
        // Owner can view
        if ($certificate->user_id === $user->id) {
            return true;
        }

        // Admin can view all certificates
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$certificate->relationLoaded('course')) {
            $certificate->load('course');
        }

        // Guru can view certificates from their own courses
        if ($user->isGuru() && $certificate->course) {
            return $certificate->course->instructor_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can create certificates.
     */
    public function create(User $user): bool
    {
        // Admin, guru, and siswa can create certificates
        // Usually generated automatically, but can be manually created
        return $user->isAdmin() || $user->isGuru() || $user->isSiswa();
    }

    /**
     * Determine if the user can update the certificate.
     */
    public function update(User $user, Certificate $certificate): bool
    {
        // Admin can update all certificates
        if ($user->isAdmin()) {
            return true;
        }

        // Ensure course relationship is loaded to avoid N+1 queries
        if (!$certificate->relationLoaded('course')) {
            $certificate->load('course');
        }

        // Guru can update certificates from their own courses
        if ($user->isGuru() && $certificate->course) {
            return $certificate->course->instructor_id === $user->id;
        }

        // Siswa cannot update certificates
        return false;
    }

    /**
     * Determine if the user can delete the certificate.
     */
    public function delete(User $user, Certificate $certificate): bool
    {
        // Only admin can delete certificates
        return $user->isAdmin();
    }

    /**
     * Determine if the user can download the certificate.
     */
    public function download(User $user, Certificate $certificate): bool
    {
        // Same as view
        return $this->view($user, $certificate);
    }

}

