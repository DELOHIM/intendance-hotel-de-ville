<?php

namespace App\Support;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Activity
{
    /** Journalise une action sensible (audit trail). */
    public static function log(string $action, ?Model $sujet = null, ?string $description = null): void
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'sujet_type' => $sujet ? class_basename($sujet) : null,
            'sujet_id' => $sujet?->getKey(),
            'description' => $description,
            'ip' => request()?->ip(),
        ]);
    }
}
