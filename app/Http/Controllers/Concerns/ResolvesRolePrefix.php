<?php

namespace App\Http\Controllers\Concerns;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * Trait untuk resolve role prefix secara dinamis.
 *
 * Karena guru/dosen dan siswa/mahasiswa menggunakan controller yang sama
 * tapi route prefix berbeda, trait ini membantu redirect ke route yang benar
 * berdasarkan role user yang sedang login.
 *
 * PENTING: Controller yang menggunakan trait ini HARUS dilindungi dengan
 * middleware 'auth' untuk memastikan user sudah terautentikasi.
 */
trait ResolvesRolePrefix
{
    /**
     * Get the route prefix for the authenticated user.
     *
     * @throws \RuntimeException if no user is authenticated
     */
    protected function getUserPrefix(): string
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            throw new \RuntimeException('No authenticated user found. Cannot resolve role prefix.');
        }

        return $user->getRolePrefix();
    }

    /**
     * Get the route prefix for the current user's teacher role.
     * Returns 'dosen' if user is dosen, otherwise 'guru'.
     *
     * @throws \RuntimeException if no user is authenticated
     */
    protected function getTeacherPrefix(): string
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            throw new \RuntimeException('No authenticated user found. Cannot resolve teacher prefix.');
        }

        return $user->isDosen() ? 'dosen' : 'guru';
    }

    /**
     * Get the route prefix for the current user's student role.
     * Returns 'mahasiswa' if user is mahasiswa, otherwise 'siswa'.
     *
     * @throws \RuntimeException if no user is authenticated
     */
    protected function getStudentPrefix(): string
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            throw new \RuntimeException('No authenticated user found. Cannot resolve student prefix.');
        }

        return $user->isMahasiswa() ? 'mahasiswa' : 'siswa';
    }

    /**
     * Generate a route URL with the correct role prefix for the authenticated user.
     *
     * @param string $routeSuffix Route name without prefix (e.g., 'courses.show')
     * @param mixed $parameters Route parameters
     * @return string Full route URL
     *
     * @throws \RuntimeException if no user is authenticated
     */
    protected function userRoute(string $routeSuffix, mixed $parameters = []): string
    {
        return route($this->getUserPrefix() . '.' . $routeSuffix, $parameters);
    }

    /**
     * Generate a route URL with the correct role prefix for teacher.
     *
     * @param string $routeSuffix Route name without prefix (e.g., 'courses.show')
     * @param mixed $parameters Route parameters
     * @return string Full route URL
     *
     * @throws \RuntimeException if no user is authenticated
     */
    protected function teacherRoute(string $routeSuffix, mixed $parameters = []): string
    {
        return route($this->getTeacherPrefix() . '.' . $routeSuffix, $parameters);
    }

    /**
     * Generate a route URL with the correct role prefix for student.
     *
     * @param string $routeSuffix Route name without prefix (e.g., 'courses.index')
     * @param mixed $parameters Route parameters
     * @return string Full route URL
     *
     * @throws \RuntimeException if no user is authenticated
     */
    protected function studentRoute(string $routeSuffix, mixed $parameters = []): string
    {
        return route($this->getStudentPrefix() . '.' . $routeSuffix, $parameters);
    }

    /**
     * Get the route prefix for a specific user.
     * Useful when generating routes for users other than the authenticated user.
     *
     * @param User $user The user to get the prefix for
     * @return string The role prefix (e.g., 'guru', 'dosen', 'siswa', 'mahasiswa')
     */
    protected function getPrefixForUser(User $user): string
    {
        return $user->getRolePrefix();
    }

    /**
     * Generate a route URL for a specific user's role.
     * Useful when admins need to generate routes for other users.
     *
     * @param User $user The user to generate the route for
     * @param string $routeSuffix Route name without prefix
     * @param mixed $parameters Route parameters
     * @return string Full route URL
     */
    protected function routeForUser(User $user, string $routeSuffix, mixed $parameters = []): string
    {
        return route($user->getRolePrefix() . '.' . $routeSuffix, $parameters);
    }
}
