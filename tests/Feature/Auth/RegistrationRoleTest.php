<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RegistrationRoleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Payload dasar untuk request registrasi publik.
     *
     * @return array<string, mixed>
     */
    private function validRegistrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ], $overrides);
    }

    /**
     * Setiap role yang ditawarkan dropdown "Daftar Sebagai" (dan alias
     * ekivalennya) harus diterima backend dan tersimpan apa adanya.
     *
     * @return array<string, array{0: string}>
     */
    public static function allowedRoleProvider(): array
    {
        return [
            'siswa' => ['siswa'],
            'mahasiswa' => ['mahasiswa'],
            'guru' => ['guru'],
            'dosen' => ['dosen'],
        ];
    }

    #[Test]
    #[DataProvider('allowedRoleProvider')]
    public function public_registration_is_allowed_for_non_admin_roles(string $role): void
    {
        $payload = $this->validRegistrationPayload(['role' => $role]);

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::query()->where('email', $payload['email'])->first();
        $this->assertNotNull($user);
        $this->assertSame($role, $user->role);
        $this->assertTrue($user->is_active);
        $this->assertAuthenticatedAs($user);
    }

    #[Test]
    public function public_registration_defaults_to_siswa_when_role_is_omitted(): void
    {
        $payload = $this->validRegistrationPayload();

        $response = $this->post('/register', $payload);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertAuthenticated();

        $user = User::query()->where('email', $payload['email'])->first();
        $this->assertNotNull($user);
        $this->assertSame('siswa', $user->role);
        $this->assertTrue($user->is_active);
    }

    #[Test]
    public function public_registration_dropdown_offers_four_separate_role_options(): void
    {
        $response = $this->get('/register');

        $response->assertOk();
        // Dropdown "Daftar Sebagai" menawarkan 4 opsi terpisah, masing-masing
        // dengan value sendiri (siswa/mahasiswa/guru/dosen). Backend menyimpan
        // nilai apa adanya; CheckRole menangani ekivalensi alias-nya.
        $response->assertSee('value="siswa"', false);
        $response->assertSee('value="mahasiswa"', false);
        $response->assertSee('value="guru"', false);
        $response->assertSee('value="dosen"', false);
        // Label pada tiap opsi.
        $response->assertSee('>Siswa<', false);
        $response->assertSee('>Mahasiswa<', false);
        $response->assertSee('>Guru<', false);
        $response->assertSee('>Dosen<', false);
    }

    #[Test]
    public function public_registration_rejects_admin_role(): void
    {
        $payload = $this->validRegistrationPayload(['role' => 'admin']);

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => $payload['email']]);
    }

    #[Test]
    public function public_registration_rejects_unknown_role(): void
    {
        $payload = $this->validRegistrationPayload(['role' => 'superadmin']);

        $response = $this->post('/register', $payload);

        $response->assertSessionHasErrors('role');
        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => $payload['email']]);
    }
}
