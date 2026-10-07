<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->after('enrollment_form_path')->index();
            $table->foreignId('archived_by')->nullable()->after('archived_at')
                ->constrained('users')->nullOnDelete();
            $table->string('archive_reason', 20)->nullable()->after('archived_by');   // passed | dropped
            $table->text('archive_remarks')->nullable()->after('archive_reason');
            $table->uuid('archive_batch')->nullable()->after('archive_remarks')->index();
            // So "Restore" puts the login back exactly as it was before archiving.
            $table->boolean('user_status_before_archive')->nullable()->after('archive_batch');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['archived_by']);
            $table->dropIndex(['archived_at']);
            $table->dropIndex(['archive_batch']);
            $table->dropColumn([
                'archived_at',
                'archived_by',
                'archive_reason',
                'archive_remarks',
                'archive_batch',
                'user_status_before_archive',
            ]);
        });
    }
};