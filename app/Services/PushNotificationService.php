<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;

class PushNotificationService
{
    /**
     * Generate a new VAPID key pair.
     *
     * Tries multiple strategies:
     * 1. minishlink/web-push VAPID::createVapidKeys() (requires OpenSSL EC support)
     * 2. openssl_pkey_new() with explicit config (Windows workaround)
     * 3. openssl CLI command (works on most systems with openssl binary)
     *
     * @return array{publicKey: string, privateKey: string}
     * @throws \RuntimeException if all strategies fail
     */
    public function generateVapidKeys(): array
    {
        // Strategy 1: Use minishlink/web-push library
        try {
            $keys = VAPID::createVapidKeys();

            return [
                'publicKey'  => $keys['publicKey'],
                'privateKey' => $keys['privateKey'],
            ];
        } catch (\Throwable $e) {
            // Continue to fallback
        }

        // Strategy 2: openssl_pkey_new with explicit config
        try {
            return $this->generateViaPhpOpenssl();
        } catch (\Throwable $e) {
            // Continue to fallback
        }

        // Strategy 3: openssl CLI command
        try {
            return $this->generateViaOpensslCli();
        } catch (\Throwable $e) {
            throw new \RuntimeException(
                'Tidak dapat generate VAPID keys secara otomatis di environment ini. '
                . 'Silakan gunakan opsi "Input Manual" dan generate keys menggunakan command: '
                . 'php artisan vapid:generate atau gunakan tool online VAPID key generator.'
            );
        }
    }

    /**
     * Generate VAPID keys using PHP openssl_pkey_new with explicit config.
     */
    private function generateViaPhpOpenssl(): array
    {
        $configArgs = [];
        $opensslCnf = getenv('OPENSSL_CONF');
        if (!$opensslCnf) {
            $possiblePaths = [
                'C:\\Program Files\\Herd\\resources\\app.asar.unpacked\\resources\\openssl\\openssl.cnf',
                'C:\\Program Files\\PHP\\extras\\ssl\\openssl.cnf',
                '/etc/ssl/openssl.cnf',
                '/usr/local/etc/openssl/openssl.cnf',
            ];
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    $configArgs['config'] = $path;
                    break;
                }
            }
        }

        $keyResource = openssl_pkey_new(array_merge([
            'curve_name'       => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ], $configArgs));

        if ($keyResource === false) {
            throw new \RuntimeException('openssl_pkey_new failed: ' . openssl_error_string());
        }

        $details = openssl_pkey_get_details($keyResource);

        if (!$details || !isset($details['ec']['d'], $details['ec']['x'], $details['ec']['y'])) {
            throw new \RuntimeException('Failed to extract EC key details.');
        }

        $privateKey = rtrim(strtr(base64_encode($details['ec']['d']), '+/', '-_'), '=');
        $publicKeyBin = "\x04" . str_pad($details['ec']['x'], 32, "\x00", STR_PAD_LEFT)
                              . str_pad($details['ec']['y'], 32, "\x00", STR_PAD_LEFT);
        $publicKey = rtrim(strtr(base64_encode($publicKeyBin), '+/', '-_'), '=');

        return ['publicKey' => $publicKey, 'privateKey' => $privateKey];
    }

    /**
     * Generate VAPID keys using the openssl CLI binary.
     */
    private function generateViaOpensslCli(): array
    {
        // Generate EC private key in PEM format
        $privateKeyPem = shell_exec('openssl ecparam -genkey -name prime256v1 -noout 2>/dev/null');

        if (!$privateKeyPem) {
            // Try Windows-style (no stderr redirect)
            $privateKeyPem = shell_exec('openssl ecparam -genkey -name prime256v1 -noout 2>NUL');
        }

        if (!$privateKeyPem) {
            throw new \RuntimeException('openssl CLI not available or failed.');
        }

        $keyResource = openssl_pkey_get_private($privateKeyPem);

        if ($keyResource === false) {
            throw new \RuntimeException('Failed to parse generated PEM key.');
        }

        $details = openssl_pkey_get_details($keyResource);

        if (!$details || !isset($details['ec']['d'], $details['ec']['x'], $details['ec']['y'])) {
            throw new \RuntimeException('Failed to extract EC key details from CLI-generated key.');
        }

        $privateKey = rtrim(strtr(base64_encode($details['ec']['d']), '+/', '-_'), '=');
        $publicKeyBin = "\x04" . str_pad($details['ec']['x'], 32, "\x00", STR_PAD_LEFT)
                              . str_pad($details['ec']['y'], 32, "\x00", STR_PAD_LEFT);
        $publicKey = rtrim(strtr(base64_encode($publicKeyBin), '+/', '-_'), '=');

        return ['publicKey' => $publicKey, 'privateKey' => $privateKey];
    }

    /**
     * Send a Web Push Notification to all active subscriptions of a user.
     *
     * Checks the global push_notifications_enabled setting and reads VAPID
     * keys from the settings table before dispatching.
     */
    public function sendToUser(User $user, array $payload): void
    {
        // Respect the global push toggle
        if (!Setting::get('push_notifications_enabled')) {
            return;
        }

        $publicKey  = Setting::get('vapid_public_key');
        $privateKey = Setting::get('vapid_private_key');

        if (!$publicKey || !$privateKey) {
            Log::warning('PushNotificationService: VAPID keys are not configured. Skipping push notification.', [
                'user_id' => $user->id,
            ]);

            return;
        }

        $subscriptions = $user->pushSubscriptions()->get();

        foreach ($subscriptions as $subscription) {
            $this->sendToSubscription($subscription, $payload);
        }
    }

    /**
     * Send a Web Push Notification to a single PushSubscription.
     *
     * Handles push server responses:
     *  - HTTP 410 / 404 → subscription is expired/invalid, delete it
     *  - HTTP 401       → VAPID key is invalid, log error
     *  - Other errors   → log warning
     */
    public function sendToSubscription(PushSubscription $subscription, array $payload): void
    {
        $publicKey  = Setting::get('vapid_public_key');
        $privateKey = Setting::get('vapid_private_key');
        $subject    = Setting::get('vapid_subject', config('app.url'));

        if (!$publicKey || !$privateKey) {
            Log::warning('PushNotificationService: VAPID keys are not configured. Cannot send to subscription.', [
                'subscription_id' => $subscription->id,
            ]);

            return;
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject'    => $subject,
                    'publicKey'  => $publicKey,
                    'privateKey' => $privateKey,
                ],
            ]);

            $pushSubscription = new Subscription(
                $subscription->endpoint,
                $subscription->p256dh,
                $subscription->auth
            );

            $jsonPayload = json_encode([
                'title'      => $payload['title'] ?? '',
                'body'       => $payload['body'] ?? '',
                'icon'       => $payload['icon'] ?? '/images/logo.png',
                'action_url' => $payload['action_url'] ?? '/',
            ]);

            $report = $webPush->sendOneNotification($pushSubscription, $jsonPayload);

            if (!$report->isSuccess()) {
                $statusCode = $report->getResponse()?->getStatusCode();

                if ($statusCode === 410 || $statusCode === 404) {
                    // Subscription is no longer valid — clean it up
                    $subscription->delete();
                } elseif ($statusCode === 401) {
                    Log::error('PushNotificationService: VAPID key invalid (HTTP 401).', [
                        'subscription_id' => $subscription->id,
                        'endpoint'        => $subscription->endpoint,
                    ]);
                } else {
                    Log::warning('PushNotificationService: Push notification failed.', [
                        'subscription_id' => $subscription->id,
                        'endpoint'        => $subscription->endpoint,
                        'status_code'     => $statusCode,
                        'reason'          => $report->getReason(),
                    ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PushNotificationService: Exception while sending push notification.', [
                'subscription_id' => $subscription->id,
                'endpoint'        => $subscription->endpoint,
                'error'           => $e->getMessage(),
            ]);
        }
    }
}
