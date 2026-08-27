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
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->change();
            $table->string('contact_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partner_schools', function (Blueprint $table) {
            $table->string('contact_person')->nullable(false)->change();
            $table->string('contact_number')->nullable(false)->change();
        });
    }
};