<?php

namespace App\Console\Commands;

use App\Models\Rental;
use Illuminate\Console\Command;

class GenerateRentalSlugs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-rental-slugs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate slugs for existing rentals that do not have one.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $rentals = Rental::whereNull('slug')->get();

        if ($rentals->isEmpty()) {
            $this->info('All rentals already have slugs. No action needed.');
            return;
        }

        $this->info("Found {$rentals->count()} rentals without slugs. Generating now...");

        $progressBar = $this->output->createProgressBar($rentals->count());
        $progressBar->start();

        foreach ($rentals as $rental) {
            // The sluggable package automatically generates the slug on saving
            $rental->save();
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->info("\nSuccessfully generated slugs for all rentals.");
    }
}
