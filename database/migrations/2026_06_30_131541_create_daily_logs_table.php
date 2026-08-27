<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_logs', function (Blueprint $table) {

            $table->id();

            $table->foreignId('student_id')->constrained()->cascadeOnDelete();

            $table->foreignId('deployment_id')->constrained()->cascadeOnDelete();

            $table->date('date');

            $table->time('time_in');

            $table->time('time_out')->nullable();

            $table->decimal('hours_rendered',5,2)->default(0);

            $table->string('gps_location')->nullable();

            $table->text('remarks')->nullable();

            $table->enum('status',[
                'pending',
                'approved',
                'rejected'
            ])->default('pending');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_logs');
    }
};