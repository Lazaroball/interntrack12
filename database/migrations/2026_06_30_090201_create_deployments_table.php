<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deployments', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->foreignId('supervisor_id')->constrained()->cascadeOnDelete();

            $table->foreignId('coordinator_id')->constrained()->cascadeOnDelete();

            $table->foreignId('partner_school_id')->constrained()->cascadeOnDelete();

            $table->date('deployment_date');

            $table->enum('status', [
                'pending',
                'deployed',
                'completed'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deployments');
    }
};