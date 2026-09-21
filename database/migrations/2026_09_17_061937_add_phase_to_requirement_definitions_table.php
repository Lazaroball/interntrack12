<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requirement_definitions', function (Blueprint $table) {
            $table->string('phase')
                ->default('initial')
                ->after('stage');
        });
    }

    public function down(): void
    {
        Schema::table('requirement_definitions', function (Blueprint $table) {
            $table->dropColumn('phase');
        });
    }
};