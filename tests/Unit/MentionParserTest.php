<?php

use App\Models\SchoolClass;
use App\Models\User;
use App\Services\MentionParser;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(Tests\TestCase::class, RefreshDatabase::class);

beforeEach(function () {
    $this->parser = new MentionParser();
});

/**
 * Validates: CP-3 (Mention Scope Correctness)
 */

it('returns all matching users when mention is in forum context', function () {
    $author = User::factory()->create(['role' => 'siswa', 'username' => 'author']);
    $user1 = User::factory()->create(['role' => 'siswa', 'username' => 'john']);
    $user2 = User::factory()->create(['role' => 'guru', 'username' => 'jane']);
    $user3 = User::factory()->create(['role' => 'admin', 'username' => 'admin_user']);

    $content = 'Hello @john and @jane and @admin_user!';

    $result = $this->parser->parse($content, 'forum', null, $author->id);

    expect($result)->toHaveCount(3);
    expect($result->pluck('id')->toArray())->toContain($user1->id, $user2->id, $user3->id);
});

it('returns only users with same school_class_id in material_comment context', function () {
    $schoolClass = SchoolClass::create([
        'name' => 'Kelas A',
        'education_level' => 'sma',
        'capacity' => 30,
    ]);

    $otherClass = SchoolClass::create([
        'name' => 'Kelas B',
        'education_level' => 'sma',
        'capacity' => 30,
    ]);

    $author = User::factory()->create([
        'role' => 'siswa',
        'username' => 'author',
        'school_class_id' => $schoolClass->id,
    ]);

    $sameClassUser = User::factory()->create([
        'role' => 'siswa',
        'username' => 'same_class',
        'school_class_id' => $schoolClass->id,
    ]);

    $differentClassUser = User::factory()->create([
        'role' => 'siswa',
        'username' => 'diff_class',
        'school_class_id' => $otherClass->id,
    ]);

    $content = 'Hey @same_class and @diff_class!';

    $result = $this->parser->parse($content, 'material_comment', $author->school_class_id, $author->id);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($sameClassUser->id);
});

it('returns empty collection when author has null school_class_id in material_comment context', function () {
    $author = User::factory()->create([
        'role' => 'siswa',
        'username' => 'author',
        'school_class_id' => null,
    ]);

    $user = User::factory()->create(['role' => 'siswa', 'username' => 'target_user']);

    $content = 'Hey @target_user!';

    $result = $this->parser->parse($content, 'material_comment', null, $author->id);

    expect($result)->toBeEmpty();
});

it('excludes self-mention from results', function () {
    $author = User::factory()->create(['role' => 'siswa', 'username' => 'author']);
    $other = User::factory()->create(['role' => 'siswa', 'username' => 'other']);

    $content = 'Mentioning myself @author and @other';

    $result = $this->parser->parse($content, 'forum', null, $author->id);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($other->id);
});

it('excludes unregistered usernames from results', function () {
    $author = User::factory()->create(['role' => 'siswa', 'username' => 'author']);
    $existing = User::factory()->create(['role' => 'siswa', 'username' => 'existing_user']);

    $content = 'Hey @existing_user and @nonexistent_user!';

    $result = $this->parser->parse($content, 'forum', null, $author->id);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($existing->id);
});

it('returns user only once even if mentioned multiple times', function () {
    $author = User::factory()->create(['role' => 'siswa', 'username' => 'author']);
    $user = User::factory()->create(['role' => 'siswa', 'username' => 'target']);

    $content = '@target hello @target how are you @target?';

    $result = $this->parser->parse($content, 'forum', null, $author->id);

    expect($result)->toHaveCount(1);
    expect($result->first()->id)->toBe($user->id);
});
