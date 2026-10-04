<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'supervisor_id',
        'coordinator_id',
        'partner_school_id',
        'program',          // placement type: "Field Study" or "Internship"
        'school_year',
        'semester',
        'deployment_date',
        'completed_at',
        'status',
        'remarks',
    ];

    protected $casts = [
        'deployment_date' => 'date',
        'completed_at'    => 'datetime',
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

    public function partnerSchool()
    {
        return $this->belongsTo(PartnerSchool::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(Supervisor::class);
    }

    public function coordinator()
    {
        return $this->belongsTo(Coordinator::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    /**
     * Not completed and not cancelled.
     */
    public function scopeOpen($query)
    {
        return $query->whereNull('completed_at')
                     ->where('status', '!=', 'cancelled');
    }

    /**
     * Students waiting for coordinator approval.
     */
    public function scopeWaiting($query)
    {
        return $query->whereNull('supervisor_id')
                     ->where('status', '!=', 'cancelled');
    }

    /**
     * Students already deployed.
     */
    public function scopeDeployed($query)
    {
        return $query->whereNotNull('supervisor_id')
                     ->whereNotNull('deployment_date')
                     ->where('status', '!=', 'cancelled');
    }

    public function scopeForTerm($query, $schoolYear, $semester)
    {
        return $query->where('school_year', $schoolYear)
                     ->where('semester', $semester);
    }

    /**
     * Filter by deployment TYPE (Field Study / Internship).
     * Note: this filters deployments.program, NOT the student's course.
     */
    public function scopeProgram($query, $program)
    {
        return $query->where('program', $program);
    }

    /**
     * Filter by the STUDENT'S program (BEED / BSED / BPED).
     */
    public function scopeStudentProgram($query, $program)
    {
        return $query->whereHas('student', fn ($q) => $q->where('program', $program));
    }

    /**
     * Filter by the student's block.
     * Pass "none" to find students who have no block assigned.
     */
    public function scopeInBlock($query, $block)
    {
        return $query->whereHas('student', function ($q) use ($block) {
            if ($block === 'none') {
                $q->where(fn ($b) => $b->whereNull('block')->orWhere('block', ''));
            } else {
                $q->where('block', $block);
            }
        });
    }

    public function scopePartnerSchool($query, $partnerSchoolId)
    {
        return $query->where('partner_school_id', $partnerSchoolId);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Attributes
    |--------------------------------------------------------------------------
    */

    /**
     * True once the coordinator has assigned a supervisor.
     */
    public function getIsApprovedAttribute()
    {
        return ! is_null($this->supervisor_id) && $this->status !== 'cancelled';
    }

    /**
     * True if the student can still change their school.
     */
    public function getCanEditAttribute()
    {
        return is_null($this->supervisor_id) && $this->status !== 'cancelled';
    }
}