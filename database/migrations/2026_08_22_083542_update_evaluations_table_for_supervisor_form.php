<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {

            // Evaluation date
            $table->date('evaluation_date')
                ->nullable()
                ->after('supervisor_id');

            /*
            |--------------------------------------------------------------------------
            | Area I — Communication Skills (25 points)
            |--------------------------------------------------------------------------
            */
            $table->decimal('criterion_1_score', 5, 2)->nullable();
            $table->decimal('criterion_2_score', 5, 2)->nullable();
            $table->decimal('criterion_3_score', 5, 2)->nullable();
            $table->decimal('criterion_4_score', 5, 2)->nullable();
            $table->decimal('criterion_5_score', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Area II — Knowledge of the Subject Matter (20 points)
            |--------------------------------------------------------------------------
            */
            $table->decimal('criterion_6_score', 5, 2)->nullable();
            $table->decimal('criterion_7_score', 5, 2)->nullable();
            $table->decimal('criterion_8_score', 5, 2)->nullable();
            $table->decimal('criterion_9_score', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Area III — Teaching Methods & Classroom Management (30 points)
            |--------------------------------------------------------------------------
            */
            $table->decimal('criterion_10_score', 5, 2)->nullable();
            $table->decimal('criterion_11_score', 5, 2)->nullable();
            $table->decimal('criterion_12_score', 5, 2)->nullable();
            $table->decimal('criterion_13_score', 5, 2)->nullable();
            $table->decimal('criterion_14_score', 5, 2)->nullable();
            $table->decimal('criterion_15_score', 5, 2)->nullable();
            $table->decimal('criterion_16_score', 5, 2)->nullable();
            $table->decimal('criterion_17_score', 5, 2)->nullable();
            $table->decimal('criterion_18_score', 5, 2)->nullable();
            $table->decimal('criterion_19_score', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Area IV — Teacher's Personality & Poise (10 points)
            |--------------------------------------------------------------------------
            */
            $table->decimal('criterion_20_score', 5, 2)->nullable();
            $table->decimal('criterion_21_score', 5, 2)->nullable();
            $table->decimal('criterion_22_score', 5, 2)->nullable();
            $table->decimal('criterion_23_score', 5, 2)->nullable();
            $table->decimal('criterion_24_score', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Area V — Lesson Planning (15 points)
            |--------------------------------------------------------------------------
            */
            $table->decimal('criterion_25_score', 5, 2)->nullable();
            $table->decimal('criterion_26_score', 5, 2)->nullable();
            $table->decimal('criterion_27_score', 5, 2)->nullable();

            /*
            |--------------------------------------------------------------------------
            | Supervisor evaluation total
            |--------------------------------------------------------------------------
            */
            $table->decimal('supervisor_total_score', 5, 2)
                ->nullable()
                ->after('criterion_27_score');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {

            $columns = [
                'evaluation_date',

                'criterion_1_score',
                'criterion_2_score',
                'criterion_3_score',
                'criterion_4_score',
                'criterion_5_score',

                'criterion_6_score',
                'criterion_7_score',
                'criterion_8_score',
                'criterion_9_score',

                'criterion_10_score',
                'criterion_11_score',
                'criterion_12_score',
                'criterion_13_score',
                'criterion_14_score',
                'criterion_15_score',
                'criterion_16_score',
                'criterion_17_score',
                'criterion_18_score',
                'criterion_19_score',

                'criterion_20_score',
                'criterion_21_score',
                'criterion_22_score',
                'criterion_23_score',
                'criterion_24_score',

                'criterion_25_score',
                'criterion_26_score',
                'criterion_27_score',

                'supervisor_total_score',
            ];

            $table->dropColumn($columns);
        });
    }
};