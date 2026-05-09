<?php

use App\Models\Setting;
use App\Models\User;
use App\Services\PushNotificationService;

it('admin can generate VAPID keys stored in settings', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    // Mock PushNotificationService to avoid OpenSSL EC key dependency
    $mockService = Mockery::mock(PushNotificationService::class);
    $mockService->shouldReceive('generateVapidKeys')->once()->andReturn([
        'publicKey' => 'test-public-key-base64url',
        'privateKey' => 'test-private-key-base64url',
    ]);
    $this->app->instance(PushNotificationService::class, $mockService);

    $response = $this->actingAs($admin)
        ->post(route('admin.settings.vapid.generate'), ['confirmed' => true]);

    $response->assertRedirect();

    // Verify keys are stored in settings
    $publicKey = Setting::where('key', 'vapid_public_key')->first();
    $privateKey = Setting::where('key', 'vapid_private_key')->first();
    $subject = Setting::where('key', 'vapid_subject')->first();

    expect($publicKey)->not->toBeNull();
    expect($publicKey->value)->toBe('test-public-key-base64url');
    expect($privateKey)->not->toBeNull();
    expect($privateKey->value)->toBe('test-private-key-base64url');
    expect($subject)->not->toBeNull();
});

it('toggle push notification changes setting value', function () {
    $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

    // Initially not set (or '0')
    Setting::set('push_notifications_enabled', '0', 'boolean', 'notification');

    $response = $this->actingAs($admin)
        ->post(route('admin.settings.vapid.toggle-push'));

    $response->assertOk();
    $response->assertJson(['enabled' => true]);

    // Toggle again
    $response = $this->actingAs($admin)
        ->post(route('admin.settings.vapid.toggle-push'));

    $response->assertOk();
    $response->assertJson(['enabled' => false]);
});

it('GET /api/vapid-public-key returns the correct key', function () {
    // Set a VAPID public key
    Setting::set('vapid_public_key', 'test-vapid-public-key-value', 'text', 'notification');

    $response = $this->getJson('/api/vapid-public-key');

    $response->assertOk();
    $response->assertJson(['public_key' => 'test-vapid-public-key-value']);
});

it('GET /api/vapid-public-key returns null when not configured', function () {
    // Ensure no VAPID key exists
    Setting::where('key', 'vapid_public_key')->delete();
    // Clear cache for this setting
    \Illuminate\Support\Facades\Cache::forget('setting_vapid_public_key');

    $response = $this->getJson('/api/vapid-public-key');

    $response->assertOk();
    $response->assertJson(['public_key' => null]);
});
