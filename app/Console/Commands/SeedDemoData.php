<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\DemoDataSeeder;

class SeedDemoData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:demo-data {--fresh : Delete existing demo data before seeding}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed demo data for bookings, messages, reviews and statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎯 Demo-data Seeder');
        $this->info('===================');
        
        if ($this->option('fresh')) {
            $this->info('🗑️  Lösche bestehende Demo-data...');
            $this->clearExistingDemoData();
        }

        $this->info('🚀 Starte Demo-data Generation...');
        // Run the database migrations to ensure the schema is up-to-date
        $this->call('migrate', ['--force' => true]);
        // Run the demo data seeder
        $this->call('db:seed', [
            '--class' => DemoDataSeeder::class
        ]);
        
        $this->newLine();
        $this->info('✅ Demo-Daten erfolgreich erstellt!');
        $this->info('📊 Die Plattform ist jetzt mit umfassenden Demo-Daten ausgestattet.');
        
        return 0;
    }
    
    /**
     * Clear existing demo data
     */
    private function clearExistingDemoData(): void
    {
        // Clear booking messages
        DB::table('booking_messages')->delete();
        $this->info('   • Booking Messages gelöscht');
        
        // Clear bookings
        DB::table('bookings')->delete();
        $this->info('   • Bookings gelöscht');
        
        // Clear reviews
        DB::table('reviews')->delete();
        $this->info('   • Reviews gelöscht');
        
        // Clear rental statistics if table exists
        if (Schema::hasTable('rental_statistics')) {
            DB::table('rental_statistics')->delete();
            $this->info('   • Rental Statistics gelöscht');
        }
        
        $this->info('✅ Bestehende Demo-Daten gelöscht');
        $this->newLine();
    }
}
