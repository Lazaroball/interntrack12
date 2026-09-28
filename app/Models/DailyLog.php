<?php
// TARGET PATH: app/Models/DailyLog.php
// This REPLACES your existing DailyLog.php. Only change vs. your version:
// the $casts array now has hours_rendered cast filled in (was a TODO comment).
// time_in / time_out are intentionally left uncast — they're MySQL TIME columns
// (no date part), so Laravel's 'datetime' cast would misbehave on them.
// They stay as plain "HH:MM:SS" strings; the controller combines them with
// `date` when it needs a full timestamp for the hours calculation.

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'deployment_id',
        'date',
        'time_in',
        'time_out',
        'hours_rendered',
        'gps_location',
        'remarks',
        'status',
    ];

    // In $casts, add:
protected $casts = [
    'date' => 'date',
    'hours_rendered' => 'decimal:2',   // <-- added
];

// Add these two accessors (used by the Blade view to pick which
// button to show — nothing else in the model changes):
public function getIsInProgressAttribute(): bool
{
    return $this->status === 'in_progress';
}

public function getIsCompletedAttribute(): bool
{
    return $this->status === 'completed';
}
}