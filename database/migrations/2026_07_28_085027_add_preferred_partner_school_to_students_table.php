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
        Schema::table('students', function (Blueprint $table) {

            $table->foreignId('preferred_partner_school_id')
                ->nullable()
                ->after('is_eligible')
                ->constrained('partner_schools')
                ->nullOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {

            $table->dropForeign(['preferred_partner_school_id']);
            $table->dropColumn('preferred_partner_school_id');

        });
    }
};