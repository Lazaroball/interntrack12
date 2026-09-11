<?php

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

    protected $casts = [
        'date' => 'date',
        // time_in, time_out, hours_rendered casts pending schema confirmation
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function deployment()
    {
        return $this->belongsTo(Deployment::class);
    }
}