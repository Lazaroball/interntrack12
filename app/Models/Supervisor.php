<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supervisor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'department',
        'status',
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

    public function deployments()
    {
        return $this->hasMany(Deployment::class);
    }

    public function observationSchedules()
    {
        return $this->hasMany(ObservationSchedule::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Accessors
    |--------------------------------------------------------------------------
    */

    /**
     * Supervisor Full Name
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
     * User Account Status
     */
    public function getAccountStatusAttribute()
    {
        return $this->user?->status;
    }

    /**
     * Last Login Timestamp
     */
    public function getLastLoginAttribute()
    {
        return $this->user?->last_login_at;
    }
}