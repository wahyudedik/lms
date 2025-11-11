<?php

namespace App\Console\Commands;

use App\Services\CertificateService;
use Illuminate\Console\Command;

class GenerateCertificatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'certificates:generate 
                          {--force : Force regenerate all certificates}
                          {--dry-run : Show what would be generated without actually generating}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate certificates for completed enrollments';

    protected CertificateService $certificateService;

    /**
     * Create a new command instance.
     */
    public function __construct(CertificateService $certificateService)
    {
        parent::__construct();
        $this->certificateService = $certificateService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🎓 Certificate Generation Started');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No certificates will be generated');
            $this->newLine();
        }

        // Get statistics
        $stats = $this->certificateService->getStatistics();

        $this->info('📊 Current Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Certificates', $stats['total']],
                ['Valid Certificates', $stats['valid']],
                ['Revoked Certificates', $stats['revoked']],
                ['Recent (30 days)', $stats['recent']],
                ['This Month', $stats['this_month']],
            ]
        );
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->info('✅ Dry run completed!');
            return Command::SUCCESS;
        }

        // Generate missing certificates
        $this->info('🔄 Generating missing certificates...');

        $bar = $this->output->createProgressBar();
        $bar->start();

        $count = $this->certificateService->generateMissing();

        $bar->finish();
        $this->newLine(2);

        if ($count > 0) {
            $this->info("✅ Successfully generated {$count} certificate(s)!");
        } else {
            $this->info('✅ No new certificates to generate. All completed enrollments already have certificates.');
        }

        $this->newLine();

        // Show updated statistics
        $newStats = $this->certificateService->getStatistics();
        $this->info('📊 Updated Statistics:');
        $this->table(
            ['Metric', 'Count', 'Change'],
            [
                ['Total Certificates', $newStats['total'], '+' . ($newStats['total'] - $stats['total'])],
                ['Valid Certificates', $newStats['valid'], '+' . ($newStats['valid'] - $stats['valid'])],
                ['This Month', $newStats['this_month'], '+' . ($newStats['this_month'] - $stats['this_month'])],
            ]
        );

        return Command::SUCCESS;
    }
}
