<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: backfill any row whose status isn't one of the two
        // values the Teaching Hours workflow actually uses. This covers
        // rows left over from the enum-truncation bug (stored as '' or
        // null) as well as any stray 'pending'/'approved'/'rejected'
        // value. The real state of a log is always determinable from
        // whether time_out has been recorded — no data is discarded,
        // every row keeps its date/time_in/time_out/hours_rendered as-is.
        DB::table('daily_logs')
            ->whereNotIn('status', ['in_progress', 'completed'])
            ->whereNull('time_out')
            ->update(['status' => 'in_progress']);

        DB::table('daily_logs')
            ->whereNotIn('status', ['in_progress', 'completed'])
            ->whereNotNull('time_out')
            ->update(['status' => 'completed']);

        // Step 2: narrow the enum to exactly what the workflow needs,
        // with the correct default. Laravel's schema builder can't alter
        // MySQL enum definitions directly, so this uses a raw ALTER
        // TABLE here in the migration (not in the controller/model).
        DB::statement("
            ALTER TABLE daily_logs
            MODIFY status ENUM('in_progress', 'completed')
            NOT NULL DEFAULT 'in_progress'
        ");
    }

    public function down(): void
    {
        // Revert to the original three-value enum. Every row that is
        // currently in_progress/completed is mapped back to the closest
        // original label so no row is left null or invalid. This is a
        // best-effort reversal, since 'in_progress'/'completed' don't
        // map 1:1 onto 'pending'/'approved'/'rejected' — adjust if you
        // need a different mapping when rolling back.
        DB::statement("
            ALTER TABLE daily_logs
            MODIFY status ENUM('pending', 'approved', 'rejected')
            NOT NULL DEFAULT 'pending'
        ");

        DB::table('daily_logs')
            ->where('status', 'completed')
            ->update(['status' => 'approved']);

        DB::table('daily_logs')
            ->where('status', 'in_progress')
            ->update(['status' => 'pending']);
    }
};