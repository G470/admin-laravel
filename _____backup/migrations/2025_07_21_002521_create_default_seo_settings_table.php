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
        Schema::create('default_seo_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // 'category_location', 'location_only', 'category_only', 'global'
            $table->string('scope')->nullable(); // 'country', 'category_type', etc.
            $table->string('scope_value')->nullable(); // 'DE', 'events', etc.
            
            // SEO Templates with placeholders
            $table->string('meta_title_template')->nullable();
            $table->text('meta_description_template')->nullable();
            $table->string('meta_keywords_template')->nullable();
            $table->longText('content_template')->nullable();
            
            // Additional settings
            $table->json('settings')->nullable(); // Additional configuration
            $table->integer('priority')->default(0); // Higher priority wins
            $table->enum('status', ['active', 'inactive'])->default('active');
            
            $table->timestamps();
            
            // Indexes
            $table->index(['type', 'status']);
            $table->index(['scope', 'scope_value']);
            $table->index('priority');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('default_seo_settings');
    }
};
