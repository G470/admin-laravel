<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rental_pushes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('rental_id')->constrained('rentals')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->integer('frequency')->default(7); // Pushes per day
            $table->integer('credits_per_push')->default(1); // Credits per push
            $table->integer('total_credits_needed'); // Total credits needed for campaign
            $table->integer('credits_used')->default(0); // Credits already used
            $table->enum('status', ['active', 'paused', 'completed', 'cancelled'])->default('active');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->datetime('last_push_at')->nullable();
            $table->datetime('next_push_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['vendor_id', 'status']);
            $table->index(['rental_id', 'category_id', 'location_id']);
            $table->index(['next_push_at', 'is_active', 'status']);
            $table->index('start_date');
            $table->index('end_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_pushes');
    }
};
