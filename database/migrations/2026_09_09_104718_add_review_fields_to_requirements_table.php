<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->foreignId('requirement_definition_id')->nullable()->after('student_id')
                ->constrained('requirement_definitions')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('submitted_at');
            $table->foreignId('reviewed_by')->nullable()->after('reviewed_at')
                ->constrained('coordinators')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('requirements', function (Blueprint $table) {
            $table->dropForeign(['requirement_definition_id']);
            $table->dropForeign(['reviewed_by']);
            $table->dropColumn(['requirement_definition_id', 'reviewed_at', 'reviewed_by']);
        });
    }
};