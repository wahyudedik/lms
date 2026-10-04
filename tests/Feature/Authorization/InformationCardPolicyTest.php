<?php

namespace Tests\Feature\Authorization;

use App\Models\InformationCard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InformationCardPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function makeCard(User $creator): InformationCard
    {
        return InformationCard::create([
            'created_by' => $creator->id,
            'title' => 'Test Card',
            'content' => 'Test content',
            'card_type' => 'info',
            'target_roles' => ['siswa'],
            'schedule_type' => 'always',
            'is_active' => true,
        ]);
    }

    #[Test]
    public function guru_owner_can_update_own_card()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $card = $this->makeCard($guru);

        $this->assertTrue($guru->can('update', $card));
    }

    #[Test]
    public function other_guru_cannot_update_card()
    {
        $guru1 = User::factory()->create(['role' => 'guru']);
        $guru2 = User::factory()->create(['role' => 'guru']);
        $card = $this->makeCard($guru2);

        $this->assertFalse($guru1->can('update', $card));
    }

    #[Test]
    public function admin_can_update_any_card()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $card = $this->makeCard($guru);

        $this->assertTrue($admin->can('update', $card));
    }

    #[Test]
    public function siswa_cannot_view_any_cards()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertFalse($siswa->can('viewAny', InformationCard::class));
    }

    #[Test]
    public function staff_roles_can_view_any_and_create()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $dosen = User::factory()->create(['role' => 'dosen']);

        foreach ([$admin, $guru, $dosen] as $user) {
            $this->assertTrue($user->can('viewAny', InformationCard::class));
            $this->assertTrue($user->can('create', InformationCard::class));
        }
    }

    #[Test]
    public function guru_owner_can_delete_own_card_but_not_others()
    {
        $owner = User::factory()->create(['role' => 'guru']);
        $other = User::factory()->create(['role' => 'guru']);
        $card = $this->makeCard($owner);

        $this->assertTrue($owner->can('delete', $card));
        $this->assertFalse($other->can('delete', $card));
    }
}
