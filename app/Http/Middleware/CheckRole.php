<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        // Check role with equivalence support
        // guru ≡ dosen, siswa ≡ mahasiswa
        $allowedRoles = match ($role) {
            'guru' => ['guru', 'dosen'],
            'dosen' => ['guru', 'dosen'],
            'siswa' => ['siswa', 'mahasiswa'],
            'mahasiswa' => ['siswa', 'mahasiswa'],
            default => [$role],
        };

        if (!in_array($user->role, $allowedRoles, true)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
