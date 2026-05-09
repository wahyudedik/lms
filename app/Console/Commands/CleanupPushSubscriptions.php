<?php

namespace App\Console\Commands;

use App\Models\PushSubscription;
use Illuminate\Console\Command;

class CleanupPushSubscriptions extends Command
{
    protected $signature = 'notifications:cleanup-subscriptions';

    protected $description = 'Remove push subscriptions that have not been updated in 90 days';

    public function handle(): int
    {
        $count = PushSubscription::where('updated_at', '<', now()->subDays(90))->delete();

        $this->info("Deleted {$count} expired push subscriptions.");

        return self::SUCCESS;
    }
}
