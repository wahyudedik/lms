<?php

namespace App\Console\Commands;

use App\Models\CheatingIncident;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoUnblockUsers extends Command
{
    protected $signature = 'cheating:auto-unblock
                            {--days=7 : Unblock users blocked more than this many days ago (fallback if auto_unblock_at not set)}
                            {--dry-run : Preview which users would be unblocked without making changes}';

    protected $description = 'Automatically unblock users whose block period has expired';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $fallbackDays = (int) $this->option('days');

        // Find incidents that should be auto-unblocked:
        // 1. Incidents with explicit auto_unblock_at that has passed
        // 2. Incidents with no auto_unblock_at but blocked_at older than fallback days
        $expiredIncidents = CheatingIncident::with('user')
            ->where('status', 'blocked')
            ->where(function ($q) use ($fallbackDays) {
                $q->where(function ($sub) {
                    // Explicit expiry set and has passed
                    $sub->whereNotNull('auto_unblock_at')
                        ->where('auto_unblock_at', '<=', now());
                })->orWhere(function ($sub) use ($fallbackDays) {
                    // No explicit expiry — use fallback days
                    $sub->whereNull('auto_unblock_at')
                        ->where('blocked_at', '<=', now()->subDays($fallbackDays));
                });
            })
            ->get();

        if ($expiredIncidents->isEmpty()) {
            $this->info('Tidak ada pengguna yang perlu di-unblock.');
            return self::SUCCESS;
        }

        $this->info("Ditemukan {$expiredIncidents->count()} insiden yang kedaluwarsa.");

        if ($isDryRun) {
            $this->table(
                ['Incident ID', 'User', 'Blocked At', 'Auto Unblock At'],
                $expiredIncidents->map(fn ($i) => [
                    $i->id,
                    $i->user?->name ?? 'Unknown',
                    $i->blocked_at?->format('d/m/Y H:i') ?? '—',
                    $i->auto_unblock_at?->format('d/m/Y H:i') ?? "Fallback ({$fallbackDays} hari)",
                ])
            );
            $this->warn('Dry-run mode: tidak ada perubahan yang disimpan.');
            return self::SUCCESS;
        }

        $unblocked = 0;

        DB::transaction(function () use ($expiredIncidents, $fallbackDays, &$unblocked) {
            // Resolve all expired incidents
            CheatingIncident::whereIn('id', $expiredIncidents->pluck('id'))
                ->update([
                    'status' => 'resolved',
                    'resolved_at' => now(),
                    'resolution_notes' => "Auto-unblock setelah masa blokir berakhir (fallback: {$fallbackDays} hari).",
                ]);

            // Unblock unique users
            $userIds = $expiredIncidents
                ->filter(fn ($i) => $i->user && $i->user->is_login_blocked)
                ->pluck('user_id')
                ->unique();

            foreach ($userIds as $userId) {
                $user = User::find($userId);
                if ($user?->resetLoginBlock(null, 'Auto-unblock terjadwal')) {
                    $unblocked++;
                    $this->line("  ✓ Unblocked: {$user->name} ({$user->email})");
                }
            }
        });

        $this->info("Selesai. {$unblocked} pengguna berhasil di-unblock.");

        return self::SUCCESS;
    }
}
