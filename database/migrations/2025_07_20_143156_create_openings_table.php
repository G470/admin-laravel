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
        Schema::create('openings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->comment('0=Sunday, 1=Monday, ..., 6=Saturday');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false)->comment('True if location is closed this day');
            $table->time('break_start')->nullable()->comment('Break/lunch start time');
            $table->time('break_end')->nullable()->comment('Break/lunch end time');
            $table->text('notes')->nullable()->comment('Special notes for this day');
            $table->timestamps();
            
            // Ensure unique combination of location and day
            $table->unique(['location_id', 'day_of_week']);
            
            // Add indexes for common queries
            $table->index(['location_id', 'day_of_week']);
            $table->index(['day_of_week']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('openings');
    }
};
