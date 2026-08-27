<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coordinator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_number',
        'first_name',
        'middle_name',
        'last_name',
        'department',
        'position',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    /**
     * Login account.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Deployments handled by this coordinator.
     */
    public function deployments()
    {
        return $this->hasMany(Deployment::class, 'coordinator_id');
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

    public function getAccountStatusAttribute()
    {
        return $this->user?->status;
    }

    public function getLastLoginAttribute()
    {
        return $this->user?->last_login_at;
    }
}