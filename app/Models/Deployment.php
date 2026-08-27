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
    'program',
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
     * Students waiting for coordinator approval.
     */
    public function scopeWaiting($query)
    {
        return $query->whereNull('supervisor_id');
    }

    /**
     * Students already deployed.
     */
   public function scopeDeployed($query)
{
    return $query->whereNotNull('supervisor_id')
                 ->whereNotNull('deployment_date');
}

    /**
     * Filter by academic term.
     */
    public function scopeForTerm($query, $schoolYear, $semester)
    {
        return $query->where('school_year', $schoolYear)
                     ->where('semester', $semester);
    }

    /**
     * Filter by program.
     */
    public function scopeProgram($query, $program)
    {
        return $query->where('program', $program);
    }

    /**
     * Filter by partner school.
     */
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
     * Returns true once the coordinator has assigned a supervisor.
     */
    public function getIsApprovedAttribute()
    {
        return !is_null($this->supervisor_id);
    }

    /**
     * Returns true if the student can still change their school.
     */
    public function getCanEditAttribute()
    {
        return is_null($this->supervisor_id);
    }
}