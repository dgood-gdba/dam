<?php

namespace App\Services;

use App\Models\Audit;

class AuditLogger
{
    public static function log(string $action, $subject = null, array $props = []): void
    {
        Audit::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'action' => $action,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->id,
            'ip' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'properties' => $props,
        ]);
    }
}
