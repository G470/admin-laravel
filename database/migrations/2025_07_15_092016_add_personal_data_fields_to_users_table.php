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
            // Add only the missing fields that don't exist yet
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('users', 'mobile')) {
                $table->string('mobile')->nullable();
            }
            if (!Schema::hasColumn('users', 'company_description')) {
                $table->text('company_description')->nullable();
            }
            if (!Schema::hasColumn('users', 'company_logo')) {
                $table->string('company_logo')->nullable();
            }
            if (!Schema::hasColumn('users', 'company_banner')) {
                $table->string('company_banner')->nullable();
            }
            if (!Schema::hasColumn('users', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('users', 'postal_code')) {
                $table->string('postal_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('users', 'billing_street')) {
                $table->string('billing_street')->nullable();
            }
            if (!Schema::hasColumn('users', 'billing_house_number')) {
                $table->string('billing_house_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'billing_address_addition')) {
                $table->string('billing_address_addition')->nullable();
            }
            if (!Schema::hasColumn('users', 'billing_city')) {
                $table->string('billing_city')->nullable();
            }
            if (!Schema::hasColumn('users', 'billing_postal_code')) {
                $table->string('billing_postal_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'billing_country')) {
                $table->string('billing_country')->nullable();
            }
            if (!Schema::hasColumn('users', 'vat_number')) {
                $table->string('vat_number')->nullable();
            }
            if (!Schema::hasColumn('users', 'bank_account')) {
                $table->string('bank_account')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_image')) {
                $table->string('profile_image')->nullable();
            }
            if (!Schema::hasColumn('users', 'company_name')) {
                $table->string('company_name')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'mobile', 'company_description', 'company_logo', 
                'company_banner', 'billing_street', 'billing_house_number', 
                'billing_address_addition', 'billing_city', 'billing_postal_code', 
                'billing_country'
            ]);
        });
    }
};
