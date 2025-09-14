<?php

namespace App\Console\Commands;

use App\Models\MagicLink;
use Illuminate\Console\Command;

class CleanupExpiredMagicLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magic-links:cleanup {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired and blocked magic links';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');

        if ($isDryRun) {
            $this->info('DRY RUN MODE - No changes will be made');
        }

        // Count expired magic links
        $expiredCount = MagicLink::where('expires_at', '<', now())->count();
        $blockedExpiredCount = MagicLink::where('blocked', true)
            ->where('blocked_until', '<', now())
            ->count();

        $totalToDelete = $expiredCount + $blockedExpiredCount;

        if ($totalToDelete === 0) {
            $this->info('No expired magic links found.');

            return Command::SUCCESS;
        }

        $this->info("Found {$expiredCount} expired magic links and {$blockedExpiredCount} expired blocked magic links.");
        $this->info("Total to delete: {$totalToDelete}");

        if ($isDryRun) {
            $this->info('Dry run complete. Use without --dry-run to actually delete the records.');

            return Command::SUCCESS;
        }

        if ($this->confirm('Do you want to delete these expired magic links?')) {
            $deletedCount = MagicLink::cleanupExpired();
            $this->info("Successfully deleted {$deletedCount} expired magic links.");
        } else {
            $this->info('Cleanup cancelled.');
        }

        return Command::SUCCESS;
    }
}
