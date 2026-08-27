<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('observation_schedules', 'observation_time')) {
            Schema::table('observation_schedules', function (Blueprint $table) {
                $table->time('observation_time')
                    ->nullable()
                    ->after('observation_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('observation_schedules', 'observation_time')) {
            Schema::table('observation_schedules', function (Blueprint $table) {
                $table->dropColumn('observation_time');
            });
        }
    }
};