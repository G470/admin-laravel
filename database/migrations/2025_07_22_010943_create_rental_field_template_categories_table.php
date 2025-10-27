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
        Schema::create('rental_field_template_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_field_template_id')->constrained()->onDelete('cascade')->name('fk_rftc_template_id');
            $table->foreignId('category_id')->constrained('admin_categories')->onDelete('cascade')->name('fk_rftc_category_id');
            $table->timestamps();

            $table->unique(['rental_field_template_id', 'category_id'], 'rental_template_category_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_field_template_categories');
    }
};
