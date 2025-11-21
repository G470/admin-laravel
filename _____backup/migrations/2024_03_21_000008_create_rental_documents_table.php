<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rental_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rental_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('path');
            $table->string('original_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rental_documents');
    }
};