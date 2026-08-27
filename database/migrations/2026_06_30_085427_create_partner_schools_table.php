<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_schools', function (Blueprint $table) {

            $table->id();

            $table->string('school_name');

            $table->string('school_type')->default('School');

            $table->string('address');

            $table->string('contact_person');

            $table->string('contact_number');

            $table->string('email')->nullable();

            $table->boolean('status')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_schools');
    }
};