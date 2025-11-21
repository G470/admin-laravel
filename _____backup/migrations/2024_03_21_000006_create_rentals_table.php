<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rentals', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->foreignId('location_id')->constrained('locations');
            $table->foreignId('category_id')->constrained('categories');
            $table->foreignId('price_ranges_id')->constrained('price_ranges');
            $table->decimal('price_range_hour', 10, 2)->nullable();
            $table->decimal('price_range_day', 10, 2)->nullable();
            $table->decimal('price_range_once', 10, 2)->nullable();
            $table->decimal('service_fee', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('draft');
            $table->foreignId('vendor_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rentals');
    }
};