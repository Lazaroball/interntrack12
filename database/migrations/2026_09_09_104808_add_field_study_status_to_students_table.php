<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('field_study_status')->default('pending_review')->after('is_eligible');
            // Allowed values (enforced in application code, not a DB enum, for easy future extension):
            // pending_review | requirements_incomplete | requirements_approved | accepted | rejected
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn('field_study_status');
        });
    }
};