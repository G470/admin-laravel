<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('master_locations', function (Blueprint $table) {
            $table->id();
            $table->string('postcode', 10)->index(); // Postal code with index for fast search
            $table->string('city', 100)->index(); // City name with index for autocomplete
            $table->string('city_encoded', 100)->nullable(); // URL-encoded city name for SEO
            $table->string('zip', 10)->nullable(); // Alternative zip code format
            $table->string('subcity', 100)->nullable(); // District or subcity area
            $table->string('state', 100)->nullable(); // State/province
            $table->string('country', 2)->index(); // Country code (DE, AT, CH)
            $table->decimal('lat', 10, 8)->nullable(); // Latitude for mapping
            $table->decimal('lng', 11, 8)->nullable(); // Longitude for mapping
            $table->timestamps();

            // Composite indexes for performance
            $table->index(['country', 'postcode']); // Country + postcode lookup
            $table->index(['country', 'city']); // Country + city lookup
            $table->index(['postcode', 'city']); // Postcode + city for unique combinations
            
            // Ensure unique locations per country
            $table->unique(['postcode', 'city', 'country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_locations');
    }
};
