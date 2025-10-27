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
        // Add indexes to rental_fields table
        Schema::table('rental_fields', function (Blueprint $table) {
            if (!$this->indexExists('rental_fields', 'rental_fields_template_id_sort_order_index')) {
                $table->index(['template_id', 'sort_order']);
            }
            if (!$this->indexExists('rental_fields', 'rental_fields_field_type_is_filterable_index')) {
                $table->index(['field_type', 'is_filterable']);
            }
            if (!$this->indexExists('rental_fields', 'rental_fields_field_name_index')) {
                $table->index('field_name');
            }
        });

        // Add indexes to rental_field_values table
        Schema::table('rental_field_values', function (Blueprint $table) {
            if (!$this->indexExists('rental_field_values', 'rental_field_values_field_id_index')) {
                $table->index('field_id');
            }
        });

        // Add indexes to rental_field_template_categories table
        Schema::table('rental_field_template_categories', function (Blueprint $table) {
            if (!$this->indexExists('rental_field_template_categories', 'rental_field_template_categories_template_id_index')) {
                $table->index('template_id');
            }
            if (!$this->indexExists('rental_field_template_categories', 'rental_field_template_categories_category_id_index')) {
                $table->index('category_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rental_fields', function (Blueprint $table) {
            $table->dropIndex(['template_id', 'sort_order']);
            $table->dropIndex(['field_type', 'is_filterable']);
            $table->dropIndex(['field_name']);
        });

        Schema::table('rental_field_values', function (Blueprint $table) {
            $table->dropIndex(['field_id']);
        });

        Schema::table('rental_field_template_categories', function (Blueprint $table) {
            $table->dropIndex(['template_id']);
            $table->dropIndex(['category_id']);
        });
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $connection = \Illuminate\Support\Facades\DB::connection();
        
        if ($connection->getDriverName() === 'pgsql') {
            // PostgreSQL query
            $exists = $connection->select(
                "SELECT 1 FROM pg_indexes WHERE indexname = ?",
                [$indexName]
            );
            return !empty($exists);
        } else {
            // MySQL query
            $indexes = $connection->select("SHOW INDEX FROM `{$table}`");
            foreach ($indexes as $index) {
                if ($index->Key_name === $indexName) {
                    return true;
                }
            }
            return false;
        }
    }
};
