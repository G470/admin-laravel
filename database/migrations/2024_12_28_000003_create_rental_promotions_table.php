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
        Schema::create('rental_promotions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained('rentals')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('vendor_credit_id')->constrained('vendor_credits')->onDelete('restrict');
            $table->unsignedInteger('credits_spent');
            $table->enum('promotion_type', ['featured', 'highlighted', 'premium'])->default('featured');
            $table->timestamp('starts_at');
            $table->timestamp('expires_at');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['is_active', 'category_id', 'expires_at']);
            $table->index(['vendor_id', 'is_active']);
            $table->index(['rental_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_promotions');
    }
};