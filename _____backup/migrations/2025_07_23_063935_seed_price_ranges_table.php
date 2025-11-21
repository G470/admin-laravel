<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert basic price range types
        DB::table('price_ranges')->insert([
            [
                'id' => 1,
                'name' => 'Stundenpreis',
                'slug' => 'hourly',
                'description' => 'Preis pro Stunde',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'Tagespreis',
                'slug' => 'daily',
                'description' => 'Preis pro Tag',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'Einmalpreis',
                'slug' => 'once',
                'description' => 'Einmaliger Preis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'name' => 'Saisonpreis',
                'slug' => 'seasonal',
                'description' => 'Saisonabhängiger Preis',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('price_ranges')->whereIn('id', [1, 2, 3, 4])->delete();
    }
};
