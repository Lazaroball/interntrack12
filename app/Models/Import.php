<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Import extends Model
{
    protected $fillable = [
    'file_name',
    'imported_by',
    'total_records',
    'successful_records',
    'failed_records',
    'duplicate_records',
];

    /**
     * The coordinator (user) who performed the import.
     */
    public function importedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}