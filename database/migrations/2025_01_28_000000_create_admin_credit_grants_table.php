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
        Schema::create('admin_credit_grants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('credit_package_id')->constrained('credit_packages')->onDelete('cascade');
            $table->integer('credits_granted');
            $table->enum('grant_type', ['admin_grant', 'compensation', 'bonus', 'correction']);
            $table->string('reason', 500);
            $table->text('internal_note')->nullable();
            $table->datetime('grant_date');
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('completed');
            $table->timestamps();

            // Indexes for performance
            $table->index(['admin_id', 'created_at']);
            $table->index(['vendor_id', 'created_at']);
            $table->index(['grant_type', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index('grant_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_credit_grants');
    }
};