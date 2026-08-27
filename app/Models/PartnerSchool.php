<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $school_name
 * @property int|null $max_slots
 * @property string $school_type
 * @property string|null $other_school_type
 * @property string|null $address
 * @property string|null $contact_person
 * @property string|null $contact_number
 * @property string|null $email
 * @property string|null $moa_file
 * @property string|null $moa_status
 * @property \Illuminate\Support\Carbon|null $moa_started_at
 * @property \Illuminate\Support\Carbon|null $moa_expires_at
 * @property int|null $available_slots
 * @property string|null $latitude
 * @property string|null $longitude
 * @property int|null $radius_meters
 * @property bool $accepting_interns
 * @property string|null $remarks
 */
class PartnerSchool extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'school_name',
        'max_slots',
        'school_type',
        'other_school_type',
        'address',
        'contact_person',
        'contact_number',
        'email',
        'moa_file',
        'moa_status',
        'moa_started_at',
        'moa_expires_at',
        'available_slots',
        'latitude',
        'longitude',
        'radius_meters',
        'accepting_interns',
        'remarks',
    ];

    protected $casts = [
        'accepting_interns' => 'boolean',
        'available_slots'   => 'integer',
        'max_slots'         => 'integer',
        'latitude'          => 'decimal:8',
        'longitude'         => 'decimal:8',
        'moa_started_at'    => 'date',
        'moa_expires_at'    => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Slot Management
    |--------------------------------------------------------------------------
    */

    public function getOccupiedSlotsAttribute(): int
    {
        if (array_key_exists('occupied_slots', $this->attributes)) {
            return (int) $this->attributes['occupied_slots'];
        }

        return $this->deployments()
            ->whereIn('status', ['pending', 'deployed'])
            ->count();
    }

    public function getRemainingSlotsAttribute(): int
    {
        $limit = $this->max_slots ?? $this->available_slots ?? 0;

        $remaining = $limit - $this->occupied_slots;

        return max(0, $remaining);
    }

    public function isFull(): bool
    {
        return $this->remaining_slots <= 0;
    }

    /*
    |--------------------------------------------------------------------------
    | MOA Management
    |--------------------------------------------------------------------------
    */

    /**
     * Determine whether the MOA has expired.
     *
     * An MOA with no expiration date is considered invalid/expired
     * because the system cannot verify that it is currently valid.
     */
    public function isMoaExpired(): bool
    {
        if (!$this->moa_expires_at) {
            return true;
        }

        return $this->moa_expires_at->isPast();
    }

    /**
     * Determine whether the partner school's MOA is currently valid.
     *
     * A valid MOA must:
     * - Have active status
     * - Have a start date
     * - Have an expiration date
     * - Not have expired
     */
    public function hasValidMoa(): bool
    {
        if ($this->moa_status !== 'active') {
            return false;
        }

        if (!$this->moa_started_at || !$this->moa_expires_at) {
            return false;
        }

        if ($this->moa_started_at->isFuture()) {
            return false;
        }

        return $this->moa_expires_at->isFuture();
    }

    /**
     * Determine whether this school can accept new interns.
     *
     * A school must:
     * - Be accepting interns
     * - Have a valid MOA
     * - Have available slots
     */
    public function canAcceptNewInterns(): bool
    {
        return $this->accepting_interns
            && $this->hasValidMoa()
            && !$this->isFull();
    }
}