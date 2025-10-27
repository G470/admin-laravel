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
        Schema::create('rental_push_credit_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_push_id')->constrained('rental_pushes')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->integer('credits_used');
            $table->datetime('push_executed_at');
            $table->datetime('next_push_at')->nullable();
            $table->string('transaction_reference')->nullable();
            $table->timestamps();

            // Indexes with shorter names
            $table->index(['vendor_id', 'push_executed_at'], 'rpct_vendor_executed_idx');
            $table->index(['rental_push_id', 'push_executed_at'], 'rpct_push_executed_idx');
            $table->index('push_executed_at', 'rpct_executed_at_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_push_credit_transactions');
    }
};
