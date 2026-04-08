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
        ?int $actorUserId = null
    ): void {
        try {
            Audit::create([
                'actor_user_id' => $actorUserId ?? auth()->id(),
                'action' => $action,
                'entity_type' => $subject ? class_basename($subject) : null,
                'entity_id' => $subject?->getKey(),
                'description' => $description,
            ]);
        } catch (Throwable $exception) {
            // La auditoria nunca debe romper el flujo principal.
            report($exception);
        }
    }
}
