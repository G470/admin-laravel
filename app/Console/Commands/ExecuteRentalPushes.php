<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RentalPush;
use Illuminate\Support\Facades\Log;

class ExecuteRentalPushes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rental-pushes:execute {--limit=50 : Maximum number of pushes to execute}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute due rental pushes and move articles to top of search results';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting rental push execution...');

        $limit = $this->option('limit');
        $duePushes = RentalPush::dueForPush()
            ->with(['rental', 'category', 'location', 'vendor'])
            ->limit($limit)
            ->get();

        if ($duePushes->isEmpty()) {
            $this->info('No rental pushes due for execution.');
            return 0;
        }

        $this->info("Found {$duePushes->count()} rental pushes to execute.");

        $successCount = 0;
        $errorCount = 0;

        foreach ($duePushes as $push) {
            try {
                $this->line("Executing push #{$push->id} for rental '{$push->rental->title}'...");

                $push->executePush();

                $this->info("✓ Push #{$push->id} executed successfully");
                $successCount++;

                // Log the push execution for search ranking
                $this->logPushForSearchRanking($push);

            } catch (\Exception $e) {
                $this->error("✗ Push #{$push->id} failed: {$e->getMessage()}");
                $errorCount++;

                Log::error('Rental push execution failed', [
                    'push_id' => $push->id,
                    'rental_id' => $push->rental_id,
                    'vendor_id' => $push->vendor_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info("Push execution completed: {$successCount} successful, {$errorCount} failed");

        return 0;
    }

    /**
     * Log push execution for search ranking system
     */
    protected function logPushForSearchRanking(RentalPush $push)
    {
        // This is where you would implement the logic to actually move the article
        // to the top of search results for the specific category and location

        $searchData = [
            'rental_id' => $push->rental_id,
            'category_id' => $push->category_id,
            'location_id' => $push->location_id,
            'push_id' => $push->id,
            'vendor_id' => $push->vendor_id,
            'executed_at' => now(),
            'next_push_at' => $push->next_push_at
        ];

        // Store in cache or database for search ranking
        $cacheKey = "rental_push_{$push->rental_id}_{$push->category_id}_{$push->location_id}";
        cache()->put($cacheKey, $searchData, now()->addHours(24 / $push->frequency));

        Log::info('Rental push logged for search ranking', $searchData);
    }
}
