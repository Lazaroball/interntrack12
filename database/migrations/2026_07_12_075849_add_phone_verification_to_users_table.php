<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('phone_verified')
                ->default(false)
                ->after('mobile_number');

            $table->boolean('allow_sms_notifications')
                ->default(true)
                ->after('phone_verified');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone_verified',
                'allow_sms_notifications',
            ]);
        });
    }
};