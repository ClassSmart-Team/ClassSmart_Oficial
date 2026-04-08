<?php

namespace App\Services;

use App\Models\Audit;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AuditLogger
{
    public static function record(
        string $action,
        ?string $description = null,
        ?Model $subject = null,
        array $metadata = [],
        ?int $actorUserId = null
    ): void {
        try {
            $request = request();

            Audit::create([
                'actor_user_id' => $actorUserId ?? auth()->id(),
                'action' => $action,
                'entity_type' => $subject ? class_basename($subject) : null,
                'entity_id' => $subject?->getKey(),
                'description' => $description,
                'metadata' => !empty($metadata) ? $metadata : null,
                'ip_address' => $request?->ip(),
                'user_agent' => $request?->userAgent(),
            ]);
        } catch (Throwable $exception) {
            // La auditoria nunca debe romper el flujo principal.
            report($exception);
        }
    }
}
