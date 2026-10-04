<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE requirements MODIFY COLUMN status ENUM('pending','approved','rejected','resubmitted') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::table('requirements')->where('status', 'resubmitted')->update(['status' => 'pending']);

        DB::statement("ALTER TABLE requirements MODIFY COLUMN status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending'");
    }
};