<?php

namespace App\Helpers;

use App\Models\ActivityLog;

class ActivityLogger
{
    public static function log(
        string $action,
        string $module,
        string $description,
        $subject = null
    ): void {
        ActivityLog::create([
            'user_id'      => auth()->id(),
            'action'       => $action,
            'module'       => $module,
            'description'  => $description,
            'ip_address'   => request()->ip(),
            'subject_type' => $subject ? get_class($subject) : null,
            'subject_id'   => $subject->id ?? null,
        ]);
    }
}