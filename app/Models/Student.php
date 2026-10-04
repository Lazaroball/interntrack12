<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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

        // Internship stage
        'internship_status',
        'internship_coordinator_passed_at',
        'internship_coordinator_passed_by',
        'internship_supervisor_passed_at',
        'internship_supervisor_passed_by',
        'internship_completed_at',
    ];

    protected $casts = [
        'year_level'                       => 'integer',
        'field_study_hours'                => 'integer',
        'internship_hours'                 => 'integer',
        'is_eligible'                      => 'boolean',
        'is_imported'                      => 'boolean',
        'is_late_enrollee'                 => 'boolean',
        'field_study_completed_at'         => 'datetime',

        // Internship stage
        'internship_coordinator_passed_at' => 'datetime',
        'internship_supervisor_passed_at'  => 'datetime',
        'internship_completed_at'          => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function preferredPartnerSchool()
    {
        return $this->belongsTo(
            PartnerSchool::class,
            'preferred_partner_school_id'
        );
    }

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    public function deployment()
    {
        return $this->hasOne(Deployment::class)->latestOfMany();
    }

    public function currentDeployment()
    {
        return $this->hasOne(Deployment::class)
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->latestOfMany('id');
    }

    public function internshipDeployment()
    {
        return $this->hasOne(Deployment::class)
            ->where('program', 'Internship')
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->latestOfMany('id');
    }

    public function fieldStudyDeployment()
    {
        return $this->hasOne(Deployment::class)
            ->where('program', 'Field Study')
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->latestOfMany('id');
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    public function getFullNameAttribute(): string
    {
        return trim(
            $this->first_name . ' ' .
            ($this->middle_name ? $this->middle_name . ' ' : '') .
            $this->last_name
        );
    }

    public function getFieldStudyStatusLabelAttribute(): string
    {
        return match ($this->field_study_status) {
            'pending_review'          => 'Pending Review',
            'requirements_incomplete' => 'Requirements Incomplete',
            'requirements_approved'   => 'Requirements Approved',
            'accepted'                => 'Accepted for Field Study',
            'rejected'                => 'Rejected / Needs Correction',
            default                   => 'Pending Review',
        };
    }

    public function getIsDeployedAttribute(): bool
    {
        return $this->deployments()
            ->whereNull('completed_at')
            ->where('status', '!=', 'cancelled')
            ->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | Internship Stage
    |--------------------------------------------------------------------------
    */

    public function getInternshipStatusLabelAttribute(): string
    {
        return match ($this->internship_status) {
            'locked'                  => 'Locked (Field Study not completed)',
            'pending_review'          => 'Pending Review',
            'requirements_incomplete' => 'Requirements Incomplete',
            'accepted'                => 'Accepted for Internship',
            'rejected'                => 'Rejected / Needs Correction',
            default                   => 'Locked (Field Study not completed)',
        };
    }

    /**
     * Internship is reachable ONLY after the coordinator cleared Field Study
     * (field_study_completed_at is set) AND the status column is not 'locked'.
     * A stray internship_status value can no longer unlock it early.
     */
    public function getIsInternshipUnlockedAttribute(): bool
    {
        return $this->field_study_completed_at !== null
            && ($this->internship_status ?? 'locked') !== 'locked';
    }

    /**
     * Valid only when BOTH coordinator and supervisor have passed.
     */
    public function getIsInternshipValidAttribute(): bool
    {
        return $this->internship_completed_at !== null
            && $this->internship_coordinator_passed_at !== null
            && $this->internship_supervisor_passed_at !== null;
    }

    public function getHasCompletedFieldStudyAttribute(): bool
    {
        return $this->field_study_completed_at !== null;
    }

    /**
     * Student may pick an Internship school only after Internship is unlocked
     * and the initial Internship requirements are accepted.
     */
    public function getCanSelectInternshipSchoolAttribute(): bool
    {
        return $this->is_internship_unlocked
            && $this->internship_status === 'accepted'
            && $this->internship_completed_at === null;
    }

    public function hasAllRequiredApproved(string $stage, ?string $phase = null): bool
    {
        $required = RequirementDefinition::active()
            ->forStage($stage)
            ->where('is_required', true)
            ->when($phase === 'initial', fn ($q) => $q->where(
                fn ($p) => $p->where('phase', 'initial')->orWhereNull('phase')
            ))
            ->when($phase === 'ongoing', fn ($q) => $q->where('phase', 'ongoing'))
            ->pluck('id');

        if ($required->isEmpty()) {
            return true;
        }

        $approved = Requirement::where('student_id', $this->id)
            ->whereIn('requirement_definition_id', $required)
            ->where('status', 'approved')
            ->distinct()
            ->count('requirement_definition_id');

        return $approved >= $required->count();
    }

    /**
     * ONE shared way to complete Field Study. Both the coordinator button
     * and the FieldStudyRequest approval must call this.
     * Safe to call twice (does nothing the second time).
     */
    public function markFieldStudyCompleted(): void
    {
        if ($this->field_study_completed_at) {
            return;
        }

        DB::transaction(function () {
            $this->fieldStudyDeployment()->first()?->update([
                'completed_at' => now(),
                'status'       => 'completed',
            ]);

            $this->forceFill([
                'field_study_completed_at' => now(),
                'program_type'             => 'Internship',
                'internship_status'        => $this->internship_status === 'locked'
                    ? 'pending_review'
                    : $this->internship_status,
            ])->save();
        });
    }

    public function passInternshipAsCoordinator(int $coordinatorId): void
    {
        $this->assertCanPassInternship();

        DB::transaction(function () use ($coordinatorId) {
            $this->forceFill([
                'internship_coordinator_passed_at' => now(),
                'internship_coordinator_passed_by' => $coordinatorId,
            ])->save();

            $this->finalizeInternshipIfBothPassed();
        });
    }

    public function passInternshipAsSupervisor(int $supervisorId): void
    {
        $this->assertCanPassInternship();

        DB::transaction(function () use ($supervisorId) {
            $this->forceFill([
                'internship_supervisor_passed_at' => now(),
                'internship_supervisor_passed_by' => $supervisorId,
            ])->save();

            $this->finalizeInternshipIfBothPassed();
        });
    }

    protected function finalizeInternshipIfBothPassed(): void
    {
        if (! $this->internship_coordinator_passed_at || ! $this->internship_supervisor_passed_at) {
            return;
        }

        $this->internshipDeployment()->first()?->update([
            'completed_at' => now(),
            'status'       => 'completed',
        ]);

        $this->forceFill([
            'internship_completed_at' => now(),
            'status'                  => 'completed',
        ])->save();
    }

    protected function assertCanPassInternship(): void
    {
        if ($this->internship_completed_at) {
            throw new DomainException('This student has already completed the Internship.');
        }

        if (! $this->is_internship_unlocked || $this->internship_status !== 'accepted') {
            throw new DomainException('The student has not been accepted for Internship.');
        }

        $deployment = $this->internshipDeployment()->first();

        if (! $deployment || ! $deployment->is_approved) {
            throw new DomainException('The student has no active, approved Internship deployment.');
        }

        if (! $this->hasAllRequiredApproved('Internship')) {
            throw new DomainException('All required Internship requirements must be approved first.');
        }
    }
}