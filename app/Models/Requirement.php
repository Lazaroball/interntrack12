<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'requirement_definition_id', // NEW
        'requirement_name',          // kept for backward compatibility; no longer written by new submissions
        'file_path',
        'status',
        'remarks',
        'submitted_at',
        'reviewed_at',               // NEW
        'reviewed_by',               // NEW
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'reviewed_at'  => 'datetime', // NEW
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // NEW
    public function requirementDefinition()
    {
        return $this->belongsTo(RequirementDefinition::class);
    }

    // NEW
    public function reviewer()
    {
        return $this->belongsTo(Coordinator::class, 'reviewed_by');
    }
}