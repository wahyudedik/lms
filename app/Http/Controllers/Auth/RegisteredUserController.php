<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['nullable', Rule::in(['siswa', 'mahasiswa', 'guru', 'dosen'])],
            'phone' => ['nullable', 'string', 'max:20'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', 'in:laki-laki,perempuan'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'role.in' => 'Role yang dipilih tidak valid. Silakan pilih Mahasiswa (siswa/mahasiswa) atau Dosen (guru/dosen).',
        ]);

        // Public registration only allows non-admin roles. 'admin' is never
        // accepted here: admin accounts are created via seeder/admin panel.
        // Alias values (guru≡dosen, siswa≡mahasiswa) are stored as-is;
        // CheckRole middleware handles the equivalence.
        $role = $validated['role'] ?? 'siswa';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $role,
            'phone' => $request->phone,
            'birth_date' => $request->birth_date,
            'gender' => $request->gender,
            'address' => $request->address,
            'is_active' => true,
        ]);

        event(new Registered($user));

        Auth::login($user);

        // Redirect to dashboard route selector
        return redirect()->route('dashboard');
    }
}
