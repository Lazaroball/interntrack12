<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('field_study_requests', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->integer('requested_hours');

            $table->enum('status',[
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->boolean('supervisor_approval')->default(false);

            $table->boolean('coordinator_approval')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('field_study_requests');
    }
};