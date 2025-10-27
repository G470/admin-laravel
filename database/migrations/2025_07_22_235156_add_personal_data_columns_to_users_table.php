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
        Schema::table('users', function (Blueprint $table) {
            // Personal data columns
            $table->string('salutation')->nullable()->after('is_vendor');
            $table->string('first_name')->nullable()->after('salutation');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('street')->nullable()->after('last_name');
            $table->string('house_number')->nullable()->after('street');
            $table->string('address_addition')->nullable()->after('house_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'salutation',
                'first_name', 
                'last_name',
                'street',
                'house_number',
                'address_addition'
            ]);
        });
    }
};
