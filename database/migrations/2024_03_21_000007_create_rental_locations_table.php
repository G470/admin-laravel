<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rental_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            // Verhindere doppelte Einträge
            $table->unique(['rental_id', 'location_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('rental_locations');
    }
};