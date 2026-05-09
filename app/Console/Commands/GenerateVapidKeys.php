<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateVapidKeys extends Command
{
    protected $signature = 'vapid:generate';

    protected $description = 'Generate VAPID key pair for Web Push Notifications';

    public function handle(): int
    {
        $this->info('Generating VAPID keys...');
        $this->newLine();

        try {
            // Try openssl CLI first (most reliable on Windows)
            $keys = $this->generateViaOpenssl();
        } catch (\Throwable $e) {
            $this->error('Failed to generate keys: ' . $e->getMessage());
            $this->newLine();
            $this->info('Alternative: Visit https://vapidkeys.com to generate keys online,');
            $this->info('then use the "Input Manual" form in Admin > Settings > Notifikasi.');

            return self::FAILURE;
        }

        $this->info('✅ VAPID Keys generated successfully!');
        $this->newLine();
        $this->line('<fg=green>Public Key:</>');
        $this->line($keys['publicKey']);
        $this->newLine();
        $this->line('<fg=yellow>Private Key:</>');
        $this->line($keys['privateKey']);
        $this->newLine();
        $this->info('Copy these keys to Admin > Settings > Notifikasi > Input Manual.');
        $this->newLine();

        if ($this->confirm('Simpan langsung ke database settings?', true)) {
            \App\Models\Setting::set('vapid_public_key', $keys['publicKey'], 'text', 'notification');
            \App\Models\Setting::set('vapid_private_key', $keys['privateKey'], 'text', 'notification');
            \App\Models\Setting::set('vapid_subject', config('app.url'), 'text', 'notification');
            $this->info('✅ VAPID keys tersimpan ke database.');
        }

        return self::SUCCESS;
    }

    private function generateViaOpenssl(): array
    {
        // Use openssl CLI to generate EC key
        $tempKeyFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'vapid_ec_key_' . uniqid() . '.pem';

        // Try multiple openssl binary paths
        $opensslBinaries = ['openssl'];

        if (PHP_OS_FAMILY === 'Windows') {
            // Common Windows paths where openssl might be
            $windowsPaths = [
                'C:\\Program Files\\Herd\\resources\\app.asar.unpacked\\resources\\openssl\\openssl.exe',
                'C:\\Program Files\\Git\\usr\\bin\\openssl.exe',
                'C:\\Program Files\\Git\\mingw64\\bin\\openssl.exe',
                'C:\\laragon\\bin\\openssl\\openssl.exe',
            ];
            foreach ($windowsPaths as $path) {
                if (file_exists($path)) {
                    array_unshift($opensslBinaries, $path);
                    break;
                }
            }
        }

        $generated = false;
        $lastOutput = [];

        foreach ($opensslBinaries as $binary) {
            $cmd = sprintf(
                '%s ecparam -genkey -name prime256v1 -noout -out %s 2>&1',
                escapeshellarg($binary),
                escapeshellarg($tempKeyFile)
            );
            exec($cmd, $lastOutput, $returnCode);

            if ($returnCode === 0 && file_exists($tempKeyFile)) {
                $generated = true;
                break;
            }
            $lastOutput = [];
        }

        if (!$generated) {
            throw new \RuntimeException('openssl command failed: ' . implode("\n", $lastOutput));
        }

        $pem = file_get_contents($tempKeyFile);
        unlink($tempKeyFile);

        if (!$pem) {
            throw new \RuntimeException('Failed to read generated key file.');
        }

        // Parse the PEM key
        $key = openssl_pkey_get_private($pem);

        if ($key === false) {
            throw new \RuntimeException('Failed to parse PEM key: ' . openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);

        if (!$details || !isset($details['ec']['d'], $details['ec']['x'], $details['ec']['y'])) {
            throw new \RuntimeException('Failed to extract EC key components.');
        }

        // Encode to base64url format
        $privateKey = rtrim(strtr(base64_encode($details['ec']['d']), '+/', '-_'), '=');

        $publicKeyBin = "\x04"
            . str_pad($details['ec']['x'], 32, "\x00", STR_PAD_LEFT)
            . str_pad($details['ec']['y'], 32, "\x00", STR_PAD_LEFT);
        $publicKey = rtrim(strtr(base64_encode($publicKeyBin), '+/', '-_'), '=');

        return ['publicKey' => $publicKey, 'privateKey' => $privateKey];
    }
}
