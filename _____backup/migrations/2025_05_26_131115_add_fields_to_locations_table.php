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
        Schema::table('locations', function (Blueprint $table) {
            $table->string('street_address')->nullable();
            $table->string('additional_address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country', 2)->nullable();
            $table->string('phone')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_main')->default(false);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn([
                'street_address',
                'additional_address',
                'postal_code',
                'city',
                'country',
                'phone',
                'description',
                'is_main',
                'latitude',
                'longitude'
            ]);
        });
    }
};
