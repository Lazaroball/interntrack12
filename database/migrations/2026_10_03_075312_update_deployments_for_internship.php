<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    DB::statement(
        "ALTER TABLE deployments
         MODIFY status ENUM('pending','deployed','completed','cancelled')
         NOT NULL DEFAULT 'pending'"
    );
}

    public function down(): void
    {
        DB::table('deployments')->where('status', 'cancelled')->update(['status' => 'pending']);

        DB::statement(
            "ALTER TABLE deployments
             MODIFY status ENUM('pending','deployed','completed')
             NOT NULL DEFAULT 'pending'"
        );
    }
};