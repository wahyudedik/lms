<?php

use App\Http\Middleware\CheckMultipleRoles;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

/**
 * Role equivalence: dosen ≡ guru, mahasiswa ≡ siswa
 * (mirrors CheckRole middleware mapping).
 */
it('dosen passes roles guru,dosen via CheckMultipleRoles', function () {
    $user = User::factory()->create(['role' => 'dosen', 'is_active' => true]);
    Auth::login($user);

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = app(CheckMultipleRoles::class)->handle(
        $request,
        fn () => response('ok'),
        'guru', 'dosen'
    );

    expect($response->getStatusCode())->toBe(200);
});

it('mahasiswa passes roles siswa,mahasiswa via CheckMultipleRoles', function () {
    $user = User::factory()->create(['role' => 'mahasiswa', 'is_active' => true]);
    Auth::login($user);

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn () => $user);

    $response = app(CheckMultipleRoles::class)->handle(
        $request,
        fn () => response('ok'),
        'siswa', 'mahasiswa'
    );

    expect($response->getStatusCode())->toBe(200);
});

it('plain siswa fails roles guru,dosen via CheckMultipleRoles', function () {
    $user = User::factory()->create(['role' => 'siswa', 'is_active' => true]);
    Auth::login($user);

    $request = Request::create('/test', 'GET');
    $request->setUserResolver(fn () => $user);

    $this->expectException(Symfony\Component\HttpKernel\Exception\HttpException::class);

    app(CheckMultipleRoles::class)->handle(
        $request,
        fn () => response('ok'),
        'guru', 'dosen'
    );
});
