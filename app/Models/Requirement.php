<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    public const STATUS_PENDING     = 'pending';
    public const STATUS_APPROVED    = 'approved';
    public const STATUS_REJECTED    = 'rejected';
    public const STATUS_RESUBMITTED = 'resubmitted';

    /** Statuses that are waiting for the coordinator to act. */
    public const NEEDS_REVIEW = [self::STATUS_PENDING, self::STATUS_RESUBMITTED];

    protected $fillable = [
        'student_id',
        'requirement_definition_id',
        'requirement_name',          // kept for backward compatibility; no longer written by new submissions
        'file_path',
        'original_name',             // the file name the student uploaded
        'status',
        'remarks',                   // coordinator notes (shown to the student on rejection)
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function requirementDefinition()
    {
        return $this->belongsTo(RequirementDefinition::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Coordinator::class, 'reviewed_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeNeedsReview($query)
    {
        return $query->whereIn('status', self::NEEDS_REVIEW);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_APPROVED    => 'Approved',
            self::STATUS_REJECTED    => 'Rejected',
            self::STATUS_RESUBMITTED => 'Resubmitted',
            default                  => 'Pending Review',
        };
    }

    /** An approved document is final: the student can no longer change it. */
    public function getIsLockedAttribute(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function getNeedsReviewAttribute(): bool
    {
        return in_array($this->status, self::NEEDS_REVIEW, true);
    }

    /** The name the student uploaded. Older rows fall back to the stored name. */
    public function getDisplayNameAttribute(): string
    {
        return $this->original_name ?: basename((string) $this->file_path);
    }

    /** Lower-case extension of the stored file (png, docx, xlsx...). */
    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo((string) $this->file_path, PATHINFO_EXTENSION));
    }
}