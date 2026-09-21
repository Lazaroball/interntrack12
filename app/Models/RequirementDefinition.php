<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequirementDefinition extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'description',
    'stage',
    'phase',
    'semester',
    'is_required',
    'is_active',
    'created_by',
];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active'   => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function creator()
    {
        return $this->belongsTo(Coordinator::class, 'created_by');
    }

    public function submissions()
    {
        return $this->hasMany(Requirement::class, 'requirement_definition_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Query Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForStage($query, string $stage)
    {
        return $query->where('stage', $stage);
    }

    public function scopeForSemester($query, string $semester)
    {
        return $query->where('semester', $semester);
    }
}