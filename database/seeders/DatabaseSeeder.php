<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CountrySeeder;
use Database\Seeders\CategoriesTableSeeder;
use Database\Seeders\LocationSeeder;
use Database\Seeders\RentalSeeder;
use Database\Seeders\UserAndAdminSeeder;
use Database\Seeders\PriceRangeSeeder;
use Database\Seeders\FormSeeder;
use Database\Seeders\SettingsSeeder;
use Database\Seeders\ReviewsSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\CreditPackageSeeder;
use Database\Seeders\GermanPostalCodesSeeder;
use Database\Seeders\DynamicRentalFieldTemplatesSeeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   */
  public function run(): void
  {
    $this->call([
      CategoriesTableSeeder::class,
      RolePermissionSeeder::class, // Run this first to create roles and permissions
      PriceRangeSeeder::class,
      CountrySeeder::class,
      GermanPostalCodesSeeder::class, // Seed German postal codes after countries
      UserAndAdminSeeder::class, // Run this before LocationSeeder and RentalSeeder
      LocationSeeder::class,
      RentalSeeder::class,
      FormSeeder::class,
      SettingsSeeder::class,
      CreditPackageSeeder::class,
      ReviewsSeeder::class,
      DynamicRentalFieldTemplatesSeeder::class, // Run after categories are created
      
      // Demo Data Seeders (run after all base data is created)
      DemoDataSeeder::class, // Runs all demo seeders in correct order
    ]);
  }
}
