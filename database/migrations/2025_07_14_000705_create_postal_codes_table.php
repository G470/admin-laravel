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
        Schema::create('postal_codes', function (Blueprint $table) {
            $table->id();
            $table->string('country_code', 2)->index(); // DE, AT, CH
            $table->string('postal_code', 10)->index(); // Postal code (e.g., "10115", "1010", "8001")
            $table->string('city')->index(); // City name
            $table->string('region')->nullable(); // State/Region (e.g., "Berlin", "Wien", "Zürich")
            $table->string('district')->nullable(); // District/Borough if available
            $table->integer('population')->nullable()->index(); // Population for ordering
            $table->decimal('latitude', 10, 8)->nullable(); // Latitude for geolocation
            $table->decimal('longitude', 11, 8)->nullable(); // Longitude for geolocation
            $table->timestamps();
            
            // Composite indexes for efficient searching
            $table->index(['country_code', 'postal_code']);
            $table->index(['country_code', 'city']);
            $table->index(['country_code', 'population']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('postal_codes');
    }
};
