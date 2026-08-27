<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class OtherEvaluatorResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'evaluator_name',
        'final_grade',
        'grade_image_path',
        'remarks',
    ];

    protected $casts = [
        'final_grade' => 'decimal:2',
    ];

    /**
     * Get the evaluation that owns this evaluator result.
     */
    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(Evaluation::class);
    }

    /**
     * Accessor to get the full URL of the grade image.
     */
    public function getGradeImageUrlAttribute(): ?string
    {
        return $this->grade_image_path
            ? Storage::disk('public')->url($this->grade_image_path)
            : null;
    }
}