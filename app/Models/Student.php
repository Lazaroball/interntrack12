<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'student_number',
    'email',
    'mobile_number',
    'reference_number',
    'first_name',
    'middle_name',
    'last_name',
    'program',
    'program_type',
    'year_level',
    'block',
    'field_study_hours',
    'internship_hours',
    'is_eligible',
    'field_study_status',
    'preferred_partner_school_id',
    'status',
    'is_imported',
    'is_late_enrollee',
    'registration_status',
    'enrollment_form_path',
    'field_study_completed_at',
];

protected $casts = [
    'year_level' => 'integer',
    'field_study_hours' => 'integer',
    'internship_hours' => 'integer',
    'is_eligible' => 'boolean',
    'is_imported' => 'boolean',
    'is_late_enrollee' => 'boolean',
    'field_study_completed_at' => 'datetime',
];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Student's login account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Student's preferred partner school.
     */
    public function preferredPartnerSchool()
    {
        return $this->belongsTo(
            PartnerSchool::class,
            'preferred_partner_school_id'
        );
    }

    /**
     * All deployments belonging to the student.
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    /**
     * Student's current/single deployment relationship.
     *
     * This relationship is used by pages that expect:
     *
     * $student->deployment
     *
     * instead of:
     *
     * $student->deployments
     */
    public function deployment()
    {
        return $this->hasOne(Deployment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Student's complete name.
     */
    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            $this->last_name
        );
    }
    /**
 * Human-friendly label for field_study_status.
 */
public function getFieldStudyStatusLabelAttribute(): string
{
    return match ($this->field_study_status) {
        'pending_review'         => 'Pending Review',
        'requirements_incomplete' => 'Requirements Incomplete',
        'requirements_approved'  => 'Requirements Approved',
        'accepted'                => 'Accepted for Field Study',
        'rejected'                => 'Rejected / Needs Correction',
        default                   => 'Pending Review',
    };
}

    /**
     * Get the student's current active deployment.
     *
     * A deployment is considered current when it has not been completed.
     */
    public function currentDeployment()
    {
        return $this->hasOne(Deployment::class)
            ->whereNull('completed_at')
            ->latestOfMany('deployment_date');
    }

    /**
     * Determine whether the student currently has a deployment.
     */
    public function getIsDeployedAttribute(): bool
    {
        return $this->deployments()
            ->whereNull('completed_at')
            ->exists();
    }
}