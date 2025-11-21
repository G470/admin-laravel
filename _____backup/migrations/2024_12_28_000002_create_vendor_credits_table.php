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
        Schema::create('vendor_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('credit_package_id')->constrained('credit_packages')->onDelete('restrict');
            $table->unsignedInteger('credits_purchased');
            $table->unsignedInteger('credits_remaining');
            $table->decimal('amount_paid', 8, 2);
            $table->enum('payment_status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->string('payment_reference', 100)->nullable();
            $table->string('payment_provider', 50)->nullable();
            $table->timestamp('purchased_at')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['vendor_id', 'payment_status']);
            $table->index('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_credits');
    }
};