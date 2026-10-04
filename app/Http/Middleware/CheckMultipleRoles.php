<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMultipleRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (! auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is active
        if (! $user->is_active) {
            auth()->logout();

            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        // Expand role equivalences: dosen ≡ guru, mahasiswa ≡ siswa
        // (same mapping as CheckRole middleware).
        $allowedRoles = [];
        foreach ($roles as $role) {
            $allowedRoles = array_merge($allowedRoles, match ($role) {
                'guru' => ['guru', 'dosen'],
                'dosen' => ['guru', 'dosen'],
                'siswa' => ['siswa', 'mahasiswa'],
                'mahasiswa' => ['siswa', 'mahasiswa'],
                default => [$role],
            });
        }

        // Check if user has any of the required roles
        if (! in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
