<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            if (!Schema::hasColumn('deployments', 'school_year')) {
                $table->string('school_year')->nullable()->after('partner_school_id');
            }

            if (!Schema::hasColumn('deployments', 'semester')) {
                $table->string('semester')->nullable()->after('school_year');
            }

            if (!Schema::hasColumn('deployments', 'remarks')) {
                $table->text('remarks')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('deployments', function (Blueprint $table) {
            if (Schema::hasColumn('deployments', 'remarks')) {
                $table->dropColumn('remarks');
            }

            // Don't remove school_year/semester here because they
            // already existed before this migration.
        });
    }
};