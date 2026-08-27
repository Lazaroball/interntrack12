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

        $table->enum('moa_status', [
            'active',
            'expired',
            'renewal_needed',
        ])->default('active');

        $table->unsignedInteger('available_slots')
              ->default(0);

        $table->boolean('accepting_interns')
              ->default(true);

        $table->text('remarks')
              ->nullable();

        $table->dropColumn('status');
    });
}

public function down(): void
{
    Schema::table('partner_schools', function (Blueprint $table) {

        $table->tinyInteger('status')
              ->default(1);

        $table->dropColumn([
            'moa_status',
            'available_slots',
            'accepting_interns',
            'remarks',
        ]);
    });
}
};
