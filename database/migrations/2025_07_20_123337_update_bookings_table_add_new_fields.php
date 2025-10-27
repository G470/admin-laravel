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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('rental_type')->nullable()->after('status');
            $table->text('message')->nullable()->after('rental_type');
            $table->text('vendor_notes')->nullable()->after('message');
            $table->string('booking_token', 32)->unique()->nullable()->after('vendor_notes');
            $table->decimal('total_price', 10, 2)->nullable()->after('booking_token');
            $table->string('guest_email')->nullable()->after('total_price');
            $table->string('guest_name')->nullable()->after('guest_email');
            $table->string('guest_phone')->nullable()->after('guest_name');
            $table->softDeletes()->after('updated_at');
            
            $table->index('booking_token');
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropIndex(['booking_token']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropSoftDeletes();
            $table->dropColumn([
                'rental_type',
                'message',
                'vendor_notes',
                'booking_token',
                'total_price',
                'guest_email',
                'guest_name',
                'guest_phone'
            ]);
        });
    }
};
