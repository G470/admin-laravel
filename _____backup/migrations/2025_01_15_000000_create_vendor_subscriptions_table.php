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
        Schema::create('vendor_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['active', 'cancelled', 'expired', 'pending'])->default('active');
            $table->date('start_date');
            $table->date('end_date');
            $table->date('next_billing_date');
            $table->date('cancellation_deadline');
            $table->decimal('monthly_price', 10, 2);
            $table->integer('rental_count')->default(0);
            $table->integer('category_count')->default(0);
            $table->integer('location_count')->default(0);
            $table->json('pricing_breakdown')->nullable();
            $table->timestamps();

            $table->index(['vendor_id', 'status']);
            $table->index('next_billing_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_subscriptions');
    }
};