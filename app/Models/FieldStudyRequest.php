<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldStudyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'requested_hours',
        'status',
        'supervisor_approval',
        'coordinator_approval',
    ];

    protected $casts = [
        // requested_hours, supervisor_approval, coordinator_approval casts pending schema confirmation
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}