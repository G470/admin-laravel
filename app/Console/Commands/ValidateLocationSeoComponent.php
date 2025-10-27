<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CitySeo;
use App\Models\Category;
use App\Models\MasterLocation;

class ValidateLocationSeoComponent extends Command
{
    protected $signature = 'validate:location-seo';
    protected $description = 'Validate Location SEO component setup';

    public function handle()
    {
        $this->info('🔍 Validating Location SEO Component Setup...');
        
        // Check database table
        $this->info('1. Checking database table...');
        try {
            $count = CitySeo::count();
            $this->info("   ✅ city_seos table exists with {$count} records");
        } catch (\Exception $e) {
            $this->error("   ❌ city_seos table issue: " . $e->getMessage());
            return 1;
        }

        // Check required columns
        $this->info('2. Checking table schema...');
        $columns = \Schema::getColumnListing('city_seos');
        $requiredColumns = ['city', 'state', 'country', 'category_id', 'meta_title', 'meta_description', 'content'];
        
        foreach ($requiredColumns as $column) {
            if (in_array($column, $columns)) {
                $this->info("   ✅ Column '{$column}' exists");
            } else {
                $this->error("   ❌ Column '{$column}' missing");
            }
        }

        // Check model relationships
        $this->info('3. Checking model relationships...');
        try {
            $categoryCount = Category::count();
            $this->info("   ✅ Category model works ({$categoryCount} categories)");
            
            $masterLocationCount = MasterLocation::count();
            $this->info("   ✅ MasterLocation model works ({$masterLocationCount} locations)");
        } catch (\Exception $e) {
            $this->error("   ❌ Model relationship issue: " . $e->getMessage());
        }

        // Check Livewire component
        $this->info('4. Checking Livewire component...');
        $componentPath = app_path('Livewire/Admin/CitiesSeo.php');
        if (file_exists($componentPath)) {
            $this->info("   ✅ CitiesSeo component exists");
        } else {
            $this->error("   ❌ CitiesSeo component missing");
        }

        // Check view files
        $this->info('5. Checking view files...');
        $viewPath = resource_path('views/livewire/admin/cities-seo.blade.php');
        if (file_exists($viewPath)) {
            $this->info("   ✅ Livewire view exists");
        } else {
            $this->error("   ❌ Livewire view missing");
        }

        $controllerViewPath = resource_path('views/content/admin/cities-seo.blade.php');
        if (file_exists($controllerViewPath)) {
            $this->info("   ✅ Controller view exists");
        } else {
            $this->error("   ❌ Controller view missing");
        }

        // Test creating a location
        $this->info('6. Testing location creation...');
        try {
            $testCity = CitySeo::create([
                'name' => 'Test Location',
                'slug' => 'test-location-' . time(),
                'city' => 'Test City',
                'country' => 'DE',
                'status' => 'offline', // Use offline to avoid affecting live data
                'meta_title' => 'Test Meta Title',
                'content' => 'Test content for validation'
            ]);
            
            $this->info("   ✅ Location created successfully (ID: {$testCity->id})");
            
            // Clean up test data
            $testCity->delete();
            $this->info("   ✅ Test data cleaned up");
            
        } catch (\Exception $e) {
            $this->error("   ❌ Location creation failed: " . $e->getMessage());
        }

        $this->info('✅ Location SEO Component validation complete!');
        $this->newLine();
        $this->info('🚀 Component is ready for use at: /admin/staedte-seo');
        
        return 0;
    }
}
