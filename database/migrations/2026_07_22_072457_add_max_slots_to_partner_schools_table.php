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
            // Adds max capacity for each partner school (defaults to 10)
            $table->integer('max_slots')->default(10)->after('school_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->dropColumn('max_slots');
        });
    }
};