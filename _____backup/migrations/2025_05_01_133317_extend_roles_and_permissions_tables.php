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
        // Extend roles table with custom fields
        Schema::table('roles', function (Blueprint $table) {
            if (!Schema::hasColumn('roles', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('roles', 'color')) {
                $table->string('color', 7)->default('#007bff')->after('description');
            }
            if (!Schema::hasColumn('roles', 'is_protected')) {
                $table->boolean('is_protected')->default(false)->after('color');
            }
            if (!Schema::hasColumn('roles', 'metadata')) {
                $table->json('metadata')->nullable()->after('is_protected');
            }
            if (!Schema::hasColumn('roles', 'created_at')) {
                $table->timestamps();
            }
        });

        // Extend permissions table with custom fields
        Schema::table('permissions', function (Blueprint $table) {
            if (!Schema::hasColumn('permissions', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('permissions', 'group')) {
                $table->string('group')->nullable()->after('description');
            }
            if (!Schema::hasColumn('permissions', 'metadata')) {
                $table->json('metadata')->nullable()->after('group');
            }
            if (!Schema::hasColumn('permissions', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn(['description', 'color', 'is_protected', 'metadata', 'created_at', 'updated_at']);
        });

        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn(['description', 'group', 'metadata', 'created_at', 'updated_at']);
        });
    }
};