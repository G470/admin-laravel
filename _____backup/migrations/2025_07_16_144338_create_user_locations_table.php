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
        Schema::create('user_locations', function (Blueprint $table) {
            $table->id(); // Unique ID for each user location record
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User reference
            $table->foreignId('master_location_id')->constrained('master_locations')->onDelete('cascade'); // Master location reference
            $table->string('street', 255)->nullable(); // Street address specific to user
            $table->string('house_number', 20)->nullable(); // House number
            $table->string('address_line_2', 255)->nullable(); // Additional address info
            $table->string('label', 100)->nullable(); // User-defined label (e.g., "Home", "Office")
            $table->boolean('is_primary')->default(false); // Primary address flag
            $table->boolean('is_active')->default(true); // Active/inactive status
            $table->timestamps();

            // Indexes for performance
            $table->index('user_id'); // Fast user lookups
            $table->index(['user_id', 'is_primary']); // Primary address lookup
            $table->index(['user_id', 'is_active']); // Active addresses lookup
            
            // Prevent duplicate user-location combinations with same street
            $table->unique(['user_id', 'master_location_id', 'street']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_locations');
    }
};
