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
        // Default vendor contact details
        Schema::create('vendor_contact_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('users')->onDelete('cascade');

            // Contact Information Fields
            $table->string('company_name')->nullable();
            $table->enum('salutation', ['Herr', 'Frau', 'Divers'])->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();

            // Visibility Controls (show/hide in frontend)
            $table->boolean('show_company_name')->default(true);
            $table->boolean('show_salutation')->default(true);
            $table->boolean('show_first_name')->default(true);
            $table->boolean('show_last_name')->default(true);
            $table->boolean('show_street')->default(true);
            $table->boolean('show_house_number')->default(true);
            $table->boolean('show_postal_code')->default(true);
            $table->boolean('show_city')->default(true);
            $table->boolean('show_country')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_mobile')->default(true);
            $table->boolean('show_whatsapp')->default(true);
            $table->boolean('show_website')->default(true);

            $table->timestamps();

            // Ensure one default contact per vendor
            $table->unique('vendor_id');
        });

        // Location-specific contact details
        Schema::create('location_contact_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained('locations')->onDelete('cascade');
            $table->boolean('use_custom_contact_details')->default(false);

            // Contact Information Fields (same as vendor_contact_details)
            $table->string('company_name')->nullable();
            $table->enum('salutation', ['Herr', 'Frau', 'Divers'])->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('street')->nullable();
            $table->string('house_number')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->foreignId('country_id')->nullable()->constrained('countries')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('website')->nullable();

            // Visibility Controls (show/hide in frontend)
            $table->boolean('show_company_name')->default(true);
            $table->boolean('show_salutation')->default(true);
            $table->boolean('show_first_name')->default(true);
            $table->boolean('show_last_name')->default(true);
            $table->boolean('show_street')->default(true);
            $table->boolean('show_house_number')->default(true);
            $table->boolean('show_postal_code')->default(true);
            $table->boolean('show_city')->default(true);
            $table->boolean('show_country')->default(true);
            $table->boolean('show_phone')->default(true);
            $table->boolean('show_mobile')->default(true);
            $table->boolean('show_whatsapp')->default(true);
            $table->boolean('show_website')->default(true);

            $table->timestamp('contact_updated_at')->nullable();
            $table->timestamps();

            // Ensure one contact detail per location
            $table->unique('location_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_contact_details');
        Schema::dropIfExists('vendor_contact_details');
    }
};
