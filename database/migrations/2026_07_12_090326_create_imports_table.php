<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imports', function (Blueprint $table) {
    $table->id();
    $table->string('file_name');
    $table->foreignId('imported_by')
        ->constrained('users')
        ->cascadeOnDelete();

    $table->unsignedInteger('total_records')->default(0);
    $table->unsignedInteger('successful_records')->default(0);
    $table->unsignedInteger('failed_records')->default(0);
    $table->unsignedInteger('duplicate_records')->default(0);

    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
    }
};