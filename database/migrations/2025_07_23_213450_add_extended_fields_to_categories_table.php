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
        Schema::table('categories', function (Blueprint $table) {
            $table->string('meta_title')->nullable()->after('description');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('default_text_content')->nullable()->after('meta_description');
            $table->string('category_image')->nullable()->after('default_text_content');
            $table->enum('form_template_display_style', [
                'show_only_rentals',
                'show_category_details_and_subcategories',
                'show_category_details_and_rentals'
            ])->default('show_only_rentals')->after('category_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'meta_title',
                'meta_description',
                'default_text_content',
                'category_image',
                'form_template_display_style'
            ]);
        });
    }
};
