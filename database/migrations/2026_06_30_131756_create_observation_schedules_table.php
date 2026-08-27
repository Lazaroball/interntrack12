<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('observation_schedules', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->foreignId('supervisor_id')->constrained()->cascadeOnDelete();

            $table->date('observation_date');

            $table->string('venue');

            $table->text('remarks')->nullable();

            $table->enum('status',[
                'scheduled',
                'completed',
                'cancelled'
            ])->default('scheduled');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('observation_schedules');
    }
};