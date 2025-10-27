<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class PriceRangeSeeder extends Seeder
{
    public function run(): void
    {
        $ranges = [
            [
                'name' => 'pro Stunde',
                'slug' => 'stunde',
                'description' => 'Preis pro Stunde',
                'is_active' => true,
                'order' => 1,
            ],
            [
                'name' => 'pro Tag',
                'slug' => 'tag',
                'description' => 'Preis pro Tag',
                'is_active' => true,
                'order' => 2,
            ],
            [
                'name' => 'einmalig',
                'slug' => 'einmalig',
                'description' => 'Einmaliger Preis',
                'is_active' => true,
                'order' => 3,
            ],
        ];
        foreach ($ranges as $range) {
            DB::table('price_ranges')->updateOrInsert([
                'slug' => $range['slug']
            ], $range);
        }
    }
}