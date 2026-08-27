<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Student;
use App\Models\Supervisor;
use App\Models\Coordinator;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function supervisor()
    {
        return $this->hasOne(Supervisor::class);
    }

    public function coordinator()
    {
        return $this->hasOne(Coordinator::class);
    }

        /*
    |--------------------------------------------------------------------------
    | Secretary Helper Methods
    |--------------------------------------------------------------------------
    */

    public function isSecretary(): bool
    {
        return $this->role === 'secretary';
    }

    public function isCoordinatorSecretary(): bool
    {
        return $this->role === 'secretary'
            && $this->secretary_type === 'coordinator';
    }

    public function isSupervisorSecretary(): bool
    {
        return $this->role === 'secretary'
            && $this->secretary_type === 'supervisor';
    }

    /*
    |--------------------------------------------------------------------------
    | Mass Assignable Fields
    |--------------------------------------------------------------------------
    */

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'middle_name',
        'email',
        'password',
        'role',
        'status',
        'mobile_number',
        'must_change_password',
        'last_login_at',
        'secretary_type',
    ];

    /*
    |--------------------------------------------------------------------------
    | Hidden Fields
    |--------------------------------------------------------------------------
    */

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /*
    |--------------------------------------------------------------------------
    | Casts
    |--------------------------------------------------------------------------
    */

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'status'               => 'boolean',
            'must_change_password' => 'boolean',
            'last_login_at'        => 'datetime',
        ];
    }
}