<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'subject_type',
        'subject_id',
        'ip_address',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Record an action for the logged-in user. Never breaks the request if logging fails.
     */
    public static function record(string $action, string $module, string $description, ?Model $subject = null): void
    {
        if (! auth()->check()) {
            return;
        }

        try {
            static::create([
                'user_id'      => auth()->id(),
                'action'       => $action,
                'module'       => $module,
                'description'  => $description,
                'subject_type' => $subject ? $subject::class : null,
                'subject_id'   => $subject?->getKey(),
                'ip_address'   => request()->ip(),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}