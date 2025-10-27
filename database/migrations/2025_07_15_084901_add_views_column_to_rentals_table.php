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
        Schema::table('rentals', function (Blueprint $table) {
            $table->bigInteger('views')->default(0)->after('status');
            $table->bigInteger('favorites_count')->default(0)->after('views');
            $table->timestamp('last_viewed_at')->nullable()->after('favorites_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rentals', function (Blueprint $table) {
            $table->dropColumn(['views', 'favorites_count', 'last_viewed_at']);
        });
    }
};
