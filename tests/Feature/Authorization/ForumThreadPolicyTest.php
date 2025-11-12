<?php

namespace Tests\Feature\Authorization;

use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumThreadPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_users_can_view_any_threads()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', ForumThread::class));
        $this->assertTrue($guru->can('viewAny', ForumThread::class));
        $this->assertTrue($siswa->can('viewAny', ForumThread::class));
    }

    /** @test */
    public function all_users_can_view_threads()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::factory()->create();

        $this->assertTrue($admin->can('view', $thread));
        $this->assertTrue($guru->can('view', $thread));
        $this->assertTrue($siswa->can('view', $thread));
    }

    /** @test */
    public function all_users_can_create_threads()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('create', ForumThread::class));
        $this->assertTrue($guru->can('create', ForumThread::class));
        $this->assertTrue($siswa->can('create', ForumThread::class));
    }

    /** @test */
    public function admin_can_update_any_thread()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($admin->can('update', $thread));
    }

    /** @test */
    public function owner_can_update_own_thread()
    {
        $user = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('update', $thread));
    }

    /** @test */
    public function user_cannot_update_other_user_thread()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user2->id]);

        $this->assertFalse($user1->can('update', $thread));
    }

    /** @test */
    public function admin_can_delete_any_thread()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($admin->can('delete', $thread));
    }

    /** @test */
    public function owner_can_delete_own_thread()
    {
        $user = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('delete', $thread));
    }

    /** @test */
    public function admin_can_pin_thread()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ForumThread::factory()->create();

        $this->assertTrue($admin->can('pin', $thread));
    }

    /** @test */
    public function guru_can_pin_thread()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $thread = ForumThread::factory()->create();

        $this->assertTrue($guru->can('pin', $thread));
    }

    /** @test */
    public function siswa_cannot_pin_thread()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::factory()->create();

        $this->assertFalse($siswa->can('pin', $thread));
    }

    /** @test */
    public function admin_can_lock_thread()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ForumThread::factory()->create();

        $this->assertTrue($admin->can('lock', $thread));
    }

    /** @test */
    public function guru_can_lock_thread()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $thread = ForumThread::factory()->create();

        $this->assertTrue($guru->can('lock', $thread));
    }

    /** @test */
    public function siswa_cannot_lock_thread()
    {
        $siswa = User::factory()->create(['role' => 'siswa']);
        $thread = ForumThread::factory()->create();

        $this->assertFalse($siswa->can('lock', $thread));
    }
}

