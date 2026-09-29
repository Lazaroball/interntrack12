<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('student_number')->unique();
            $table->string('email')->nullable();
            $table->string('mobile_number', 20)->nullable();
            $table->string('reference_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('program');
            $table->integer('year_level')->default(4);
            $table->integer('field_study_hours')->default(0);
            $table->integer('internship_hours')->default(0);
            $table->boolean('is_eligible')->default(false);
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};