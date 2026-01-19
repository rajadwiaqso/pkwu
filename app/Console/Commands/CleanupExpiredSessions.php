<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StockReservation;
use Carbon\Carbon;

class CleanupExpiredSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stock:cleanup {--force : Force cleanup without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup expired stock reservations and checkout sessions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting cleanup of expired stock reservations...');

        // Clean expired stock reservations
        $expiredCount = StockReservation::cleanExpiredReservations();
        $this->info("Cleaned {$expiredCount} expired stock reservations.");

        // Clean expired checkout sessions (older than 1 hour)
        $expiredSessionsCount = $this->cleanExpiredCheckoutSessions();
        $this->info("Cleaned {$expiredSessionsCount} expired checkout sessions.");

        $this->info('Cleanup completed successfully!');
        
        return Command::SUCCESS;
    }

    /**
     * Clean expired checkout sessions from Laravel session storage
     */
    private function cleanExpiredCheckoutSessions()
    {
        $cleaned = 0;
        $cutoffTime = now()->subHour(); // Sessions older than 1 hour
        
        // Get all session files
        $sessionPath = storage_path('framework/sessions');
        
        if (!is_dir($sessionPath)) {
            $this->warn('Session directory not found, skipping session cleanup.');
            return 0;
        }

        $files = glob($sessionPath . '/*');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                $lastModified = Carbon::createFromTimestamp(filemtime($file));
                
                if ($lastModified->lt($cutoffTime)) {
                    // Read session content to check if it contains checkout data
                    $content = file_get_contents($file);
                    
                    if (strpos($content, 'checkout_') !== false) {
                        unlink($file);
                        $cleaned++;
                    }
                }
            }
        }

        return $cleaned;
    }
}
