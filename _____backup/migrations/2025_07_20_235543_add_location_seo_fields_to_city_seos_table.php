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
        Schema::table('city_seos', function (Blueprint $table) {
            // Add missing location fields
            $table->string('city')->nullable()->after('slug');
            $table->string('state')->nullable()->after('city');
            $table->string('country', 2)->default('DE')->after('state');
            
            // Add category relationship for location-specific SEO
            $table->foreignId('category_id')->nullable()->after('country')->constrained('categories')->onDelete('set null');
            
            // Add comprehensive SEO fields
            $table->string('meta_title')->nullable()->after('category_id');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->string('meta_keywords')->nullable()->after('meta_description');
            $table->longText('content')->nullable()->after('meta_keywords');
            $table->text('description')->nullable()->after('content');
            
            // Add geographic data
            $table->decimal('latitude', 10, 8)->nullable()->after('description');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->integer('population')->nullable()->after('longitude');
            
            // Add featured image field
            $table->string('featured_image')->nullable()->after('population');
            
            // Add indexes for performance
            $table->index(['country', 'status']);
            $table->index(['category_id', 'status']);
            $table->index(['city', 'state', 'country']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('city_seos', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['country', 'status']);
            $table->dropIndex(['category_id', 'status']);
            $table->dropIndex(['city', 'state', 'country']);
            
            // Drop foreign key
            $table->dropForeign(['category_id']);
            
            // Drop added columns
            $table->dropColumn([
                'city',
                'state', 
                'country',
                'category_id',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'content',
                'description',
                'latitude',
                'longitude',
                'population',
                'featured_image'
            ]);
        });
    }
};
