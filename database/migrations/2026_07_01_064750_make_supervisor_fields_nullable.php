<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->string('employee_number')->nullable()->change();
            $table->string('department')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('supervisors', function (Blueprint $table) {
            $table->string('employee_number')->nullable(false)->change();
            $table->string('department')->nullable(false)->change();
        });
    }
};