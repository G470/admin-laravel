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
        Schema::create('rental_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('favorites')->default(0);
            $table->integer('inquiries')->default(0);
            $table->integer('bookings')->default(0);
            $table->decimal('revenue', 10, 2)->default(0);
            $table->timestamps();
            
            $table->unique(['rental_id', 'date']);
            $table->index(['rental_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_statistics');
    }
};
