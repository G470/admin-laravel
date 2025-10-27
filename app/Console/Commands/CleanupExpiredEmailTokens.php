<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\EmailChangeToken;

class CleanupExpiredEmailTokens extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:tokens:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up expired email change tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deletedCount = EmailChangeToken::cleanupExpired();

        $this->info("Cleaned up {$deletedCount} expired email change tokens.");

        return Command::SUCCESS;
    }
}