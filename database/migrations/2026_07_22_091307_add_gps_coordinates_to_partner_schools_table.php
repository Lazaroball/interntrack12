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
        Schema::table('partner_schools', function (Blueprint $table) {
            // Decimal(10,8) handles latitude (-90.00000000 to 90.00000000)
            $table->decimal('latitude', 10, 8)->nullable()->after('address');
            
            // Decimal(11,8) handles longitude (-180.00000000 to 180.00000000)
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            
            // Allowed geofence boundary in meters (default 100 meters)
            $table->integer('radius_meters')->default(100)->after('longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'radius_meters']);
        });
    }
};