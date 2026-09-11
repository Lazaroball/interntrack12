<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('requirement_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('stage'); // 'Field Study' | 'Internship' — kept as a string, matching existing Deployment.program convention, so a future config table can replace this without a schema break
            $table->string('semester'); // matches existing free-string convention (e.g. '1st Semester', '2nd Semester')
            $table->boolean('is_required')->default(true);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('coordinators')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('requirement_definitions');
    }
};