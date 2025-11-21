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
        Schema::create('rental_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_field_template_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('label');
            $table->string('type'); // text, number, select, checkbox, radio, textarea
            $table->text('options')->nullable(); // JSON for select/radio options
            $table->text('default_value')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_filterable')->default(false);
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->integer('sort_order')->default(0);
            $table->json('validation_rules')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rental_fields');
    }
};
