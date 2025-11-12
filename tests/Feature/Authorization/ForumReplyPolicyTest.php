<?php

namespace Tests\Feature\Authorization;

use App\Models\ForumReply;
use App\Models\ForumThread;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForumReplyPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function all_users_can_view_any_replies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('viewAny', ForumReply::class));
        $this->assertTrue($guru->can('viewAny', ForumReply::class));
        $this->assertTrue($siswa->can('viewAny', ForumReply::class));
    }

    /** @test */
    public function all_users_can_view_replies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);
        $reply = ForumReply::factory()->create();

        $this->assertTrue($admin->can('view', $reply));
        $this->assertTrue($guru->can('view', $reply));
        $this->assertTrue($siswa->can('view', $reply));
    }

    /** @test */
    public function all_users_can_create_replies()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $siswa = User::factory()->create(['role' => 'siswa']);

        $this->assertTrue($admin->can('create', ForumReply::class));
        $this->assertTrue($guru->can('create', ForumReply::class));
        $this->assertTrue($siswa->can('create', ForumReply::class));
    }

    /** @test */
    public function admin_can_update_any_reply()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $reply = ForumReply::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($admin->can('update', $reply));
    }

    /** @test */
    public function owner_can_update_own_reply()
    {
        $user = User::factory()->create();
        $reply = ForumReply::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('update', $reply));
    }

    /** @test */
    public function user_cannot_update_other_user_reply()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $reply = ForumReply::factory()->create(['user_id' => $user2->id]);

        $this->assertFalse($user1->can('update', $reply));
    }

    /** @test */
    public function admin_can_delete_any_reply()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();
        $reply = ForumReply::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($admin->can('delete', $reply));
    }

    /** @test */
    public function owner_can_delete_own_reply()
    {
        $user = User::factory()->create();
        $reply = ForumReply::factory()->create(['user_id' => $user->id]);

        $this->assertTrue($user->can('delete', $reply));
    }

    /** @test */
    public function admin_can_mark_reply_as_solution()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $thread = ForumThread::factory()->create();
        $reply = ForumReply::factory()->create(['thread_id' => $thread->id]);

        $this->assertTrue($admin->can('markAsSolution', $reply));
    }

    /** @test */
    public function guru_can_mark_reply_as_solution()
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $thread = ForumThread::factory()->create();
        $reply = ForumReply::factory()->create(['thread_id' => $thread->id]);

        $this->assertTrue($guru->can('markAsSolution', $reply));
    }

    /** @test */
    public function thread_owner_can_mark_reply_as_solution()
    {
        $user = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user->id]);
        $reply = ForumReply::factory()->create(['thread_id' => $thread->id]);

        $this->assertTrue($user->can('markAsSolution', $reply));
    }

    /** @test */
    public function other_user_cannot_mark_reply_as_solution()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $thread = ForumThread::factory()->create(['user_id' => $user2->id]);
        $reply = ForumReply::factory()->create(['thread_id' => $thread->id]);

        $this->assertFalse($user1->can('markAsSolution', $reply));
    }
}

