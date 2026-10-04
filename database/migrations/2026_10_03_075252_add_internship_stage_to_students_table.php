<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // locked | pending_review | requirements_incomplete | accepted | rejected
            $table->string('internship_status')
                ->default('locked')
                ->after('field_study_completed_at');

            $table->timestamp('internship_coordinator_passed_at')->nullable()->after('internship_status');
            $table->foreignId('internship_coordinator_passed_by')
                ->nullable()
                ->after('internship_coordinator_passed_at')
                ->constrained('coordinators')
                ->nullOnDelete();

            $table->timestamp('internship_supervisor_passed_at')->nullable()->after('internship_coordinator_passed_by');
            $table->foreignId('internship_supervisor_passed_by')
                ->nullable()
                ->after('internship_supervisor_passed_at')
                ->constrained('supervisors')
                ->nullOnDelete();

            // Set only when BOTH passes exist.
            $table->timestamp('internship_completed_at')->nullable()->after('internship_supervisor_passed_by');
        });

        // Backfill existing students.
        // Already finished Field Study, or imported as Internship -> unlocked for initial review.
        DB::table('students')
            ->where(function ($q) {
                $q->whereNotNull('field_study_completed_at')
                  ->orWhere('program_type', 'Internship');
            })
            ->update(['internship_status' => 'pending_review']);
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['internship_coordinator_passed_by']);
            $table->dropForeign(['internship_supervisor_passed_by']);

            $table->dropColumn([
                'internship_status',
                'internship_coordinator_passed_at',
                'internship_coordinator_passed_by',
                'internship_supervisor_passed_at',
                'internship_supervisor_passed_by',
                'internship_completed_at',
            ]);
        });
    }
};