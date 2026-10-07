<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('requirements', 'original_name')) {
            Schema::table('requirements', function (Blueprint $table) {
                $table->string('original_name')->nullable()->after('file_path');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('requirements', 'original_name')) {
            Schema::table('requirements', function (Blueprint $table) {
                $table->dropColumn('original_name');
            });
        }
    }
};