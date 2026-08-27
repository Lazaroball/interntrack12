<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->date('moa_started_at')->nullable()->after('moa_file');
            $table->date('moa_expires_at')->nullable()->after('moa_started_at');
        });
    }

    public function down(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->dropColumn([
                'moa_started_at',
                'moa_expires_at',
            ]);
        });
    }
};