<?php
// database/migrations/xxxx_xx_xx_add_soft_deletes_to_partner_schools_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};