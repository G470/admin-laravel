<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update rental_field_templates table
        Schema::table('rental_field_templates', function (Blueprint $table) {
            $table->json('settings')->nullable()->after('sort_order');
            $table->softDeletes()->after('updated_at');
        });

        // Update rental_fields table
        Schema::table('rental_fields', function (Blueprint $table) {
            // Rename columns to match new schema
            $table->renameColumn('rental_field_template_id', 'template_id');
            $table->renameColumn('name', 'field_name');
            $table->renameColumn('label', 'field_label');
            $table->renameColumn('type', 'field_type');
            $table->renameColumn('help_text', 'field_description');
        });

        // Add new columns to rental_fields table
        Schema::table('rental_fields', function (Blueprint $table) {
            $table->json('dependencies')->nullable()->after('validation_rules');
            $table->json('seo_settings')->nullable()->after('dependencies');
            $table->boolean('is_searchable')->default(true)->after('is_filterable');

            // Drop columns that are no longer needed
            $table->dropColumn(['default_value', 'placeholder']);
        });

        // Update rental_field_values table
        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->renameColumn('rental_field_id', 'field_id');
            $table->renameColumn('value', 'field_value');
        });

        // Add new column to rental_field_values table
        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->json('additional_data')->nullable()->after('field_value');
        });

        // Update rental_field_template_categories table
        Schema::table('rental_field_template_categories', function (Blueprint $table) {
            $table->renameColumn('rental_field_template_id', 'template_id');
        });

        // Add indexes for better performance
        Schema::table('rental_fields', function (Blueprint $table) {
            $table->index(['template_id', 'sort_order']);
            $table->index(['field_type', 'is_filterable']);
            $table->index('field_name');
        });

        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->index('field_id');
            // Note: field_value is TEXT type
            // PostgreSQL: Create index on field_id and first 255 chars of field_value
            if (DB::connection()->getDriverName() === 'pgsql') {
                DB::statement('CREATE INDEX rental_field_values_field_id_field_value_partial_index ON rental_field_values (field_id, LEFT(field_value, 255))');
            } else {
                // MySQL: Use prefix index
                $table->rawIndex('field_id, field_value(255)', 'rental_field_values_field_id_field_value_partial_index');
            }
        });

        Schema::table('rental_field_template_categories', function (Blueprint $table) {
            $table->index('template_id');
            $table->index('category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove indexes
        Schema::table('rental_fields', function (Blueprint $table) {
            $table->dropIndex(['template_id', 'sort_order']);
            $table->dropIndex(['field_type', 'is_filterable']);
            $table->dropIndex(['field_name']);
        });

        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->dropIndex(['field_id']);
        });
        
        // Drop the composite index separately for cross-database compatibility
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS rental_field_values_field_id_field_value_partial_index');
        } else {
            Schema::table('rental_field_values', function (Blueprint $table) {
                $table->dropIndex('rental_field_values_field_id_field_value_partial_index');
            });
        }

        Schema::table('rental_field_template_categories', function (Blueprint $table) {
            $table->dropIndex(['template_id']);
            $table->dropIndex(['category_id']);
        });

        // Reverse rental_field_template_categories changes
        Schema::table('rental_field_template_categories', function (Blueprint $table) {
            $table->renameColumn('template_id', 'rental_field_template_id');
        });

        // Reverse rental_field_values changes
        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->dropColumn('additional_data');
        });

        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->renameColumn('field_id', 'rental_field_id');
            $table->renameColumn('field_value', 'value');
        });

        // Reverse rental_fields changes
        Schema::table('rental_fields', function (Blueprint $table) {
            $table->string('default_value')->nullable();
            $table->string('placeholder')->nullable();
            $table->dropColumn(['dependencies', 'seo_settings', 'is_searchable']);
        });

        Schema::table('rental_fields', function (Blueprint $table) {
            $table->renameColumn('template_id', 'rental_field_template_id');
            $table->renameColumn('field_name', 'name');
            $table->renameColumn('field_label', 'label');
            $table->renameColumn('field_type', 'type');
            $table->renameColumn('field_description', 'help_text');
        });

        // Reverse rental_field_templates changes
        Schema::table('rental_field_templates', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('settings');
        });
    }
};
