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
        // Add notification fields to user_locations table
        Schema::table('user_locations', function (Blueprint $table) {
            $table->string('notification_email')->nullable()->after('is_active');
            $table->boolean('use_custom_notifications')->default(false)->after('notification_email');
            $table->timestamp('notification_updated_at')->nullable()->after('use_custom_notifications');
        });

        // Add default notification fields to users table  
        Schema::table('users', function (Blueprint $table) {
            $table->string('default_notification_email')->nullable()->after('email');
            $table->json('notification_preferences')->nullable()->after('default_notification_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_locations', function (Blueprint $table) {
            $table->dropColumn([
                'notification_email',
                'use_custom_notifications',
                'notification_updated_at'
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'default_notification_email',
                'notification_preferences'
            ]);
        });
    }
};
