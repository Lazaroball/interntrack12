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
            $table->boolean('is_imported')
                  ->default(false)
                  ->after('status');

            $table->boolean('is_late_enrollee')
                  ->default(false)
                  ->after('is_imported');

            $table->enum('registration_status', [
                'pending',
                'accepted',
                'rejected',
            ])
            ->nullable()
            ->after('is_late_enrollee');

            $table->string('enrollment_form_path')
                  ->nullable()
                  ->after('registration_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'is_imported',
                'is_late_enrollee',
                'registration_status',
                'enrollment_form_path',
            ]);
        });
    }
};