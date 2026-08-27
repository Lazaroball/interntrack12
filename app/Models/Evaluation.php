<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'observation_schedule_id',
        'student_id',
        'supervisor_id',
        'evaluation_date',

        'rating',
        'comments',
        'overall_score',

        // Area I — Communication Skills
        'criterion_1_score',
        'criterion_2_score',
        'criterion_3_score',
        'criterion_4_score',
        'criterion_5_score',

        // Area II — Knowledge of the Subject Matter
        'criterion_6_score',
        'criterion_7_score',
        'criterion_8_score',
        'criterion_9_score',

        // Area III — Teaching Methods & Classroom Management
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

        // Area IV — Teacher's Personality & Poise
        'criterion_20_score',
        'criterion_21_score',
        'criterion_22_score',
        'criterion_23_score',
        'criterion_24_score',

        // Area V — Lesson Planning
        'criterion_25_score',
        'criterion_26_score',
        'criterion_27_score',

        // Computed supervisor score
        'supervisor_total_score',
    ];

    protected $casts = [
        'evaluation_date' => 'date',

        'rating' => 'decimal:2',
        'overall_score' => 'decimal:2',
        'supervisor_total_score' => 'decimal:2',

        'criterion_1_score' => 'decimal:2',
        'criterion_2_score' => 'decimal:2',
        'criterion_3_score' => 'decimal:2',
        'criterion_4_score' => 'decimal:2',
        'criterion_5_score' => 'decimal:2',

        'criterion_6_score' => 'decimal:2',
        'criterion_7_score' => 'decimal:2',
        'criterion_8_score' => 'decimal:2',
        'criterion_9_score' => 'decimal:2',

        'criterion_10_score' => 'decimal:2',
        'criterion_11_score' => 'decimal:2',
        'criterion_12_score' => 'decimal:2',
        'criterion_13_score' => 'decimal:2',
        'criterion_14_score' => 'decimal:2',
        'criterion_15_score' => 'decimal:2',
        'criterion_16_score' => 'decimal:2',
        'criterion_17_score' => 'decimal:2',
        'criterion_18_score' => 'decimal:2',
        'criterion_19_score' => 'decimal:2',

        'criterion_20_score' => 'decimal:2',
        'criterion_21_score' => 'decimal:2',
        'criterion_22_score' => 'decimal:2',
        'criterion_23_score' => 'decimal:2',
        'criterion_24_score' => 'decimal:2',

        'criterion_25_score' => 'decimal:2',
        'criterion_26_score' => 'decimal:2',
        'criterion_27_score' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function observationSchedule()
    {
        return $this->belongsTo(
            ObservationSchedule::class,
            'observation_schedule_id'
        );
    }

    public function otherEvaluatorResults()
{
    return $this->hasMany(OtherEvaluatorResult::class);
}

    /*
    |--------------------------------------------------------------------------
    | Evaluation Areas
    |--------------------------------------------------------------------------
    */

    public function getAreaOneScoreAttribute(): float
    {
        return $this->sumCriteria(1, 5);
    }

    public function getAreaTwoScoreAttribute(): float
    {
        return $this->sumCriteria(6, 9);
    }

    public function getAreaThreeScoreAttribute(): float
    {
        return $this->sumCriteria(10, 19);
    }

    public function getAreaFourScoreAttribute(): float
    {
        return $this->sumCriteria(20, 24);
    }

    public function getAreaFiveScoreAttribute(): float
    {
        return $this->sumCriteria(25, 27);
    }

    /*
    |--------------------------------------------------------------------------
    | Score Calculation
    |--------------------------------------------------------------------------
    */

    protected function sumCriteria(int $start, int $end): float
    {
        $total = 0;

        for ($i = $start; $i <= $end; $i++) {
            $total += (float) ($this->{"criterion_{$i}_score"} ?? 0);
        }

        return $total;
    }

    /**
     * Calculate the supervisor's total score.
     *
     * Maximum = 100 points.
     */
    public function calculateSupervisorTotal(): float
    {
        return round(
            $this->area_one_score +
            $this->area_two_score +
            $this->area_three_score +
            $this->area_four_score +
            $this->area_five_score,
            2
        );
    }
}