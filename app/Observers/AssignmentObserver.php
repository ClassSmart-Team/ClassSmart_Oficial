<?php

namespace App\Observers;

use App\Models\Assignment;
use App\Services\AuditLogger;

class AssignmentObserver
{
    public function created(Assignment $assignment): void
    {
        AuditLogger::record(
            'assignments.created',
            "Tarea creada: {$assignment->title}",
            $assignment,
            [
                'group_id' => $assignment->group_id,
                'unit_id' => $assignment->unit_id,
                'status' => $assignment->status,
            ]
        );
    }

    public function updated(Assignment $assignment): void
    {
        if (!$assignment->wasChanged()) {
            return;
        }

        $changes = $assignment->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $assignment->getOriginal($field);
        }

        AuditLogger::record(
            'assignments.updated',
            "Tarea editada: {$assignment->title}",
            $assignment,
            [
                'old' => $oldValues,
                'new' => $changes,
            ]
        );
    }

    public function deleted(Assignment $assignment): void
    {
        AuditLogger::record(
            'assignments.deleted',
            "Tarea eliminada: {$assignment->title}",
            $assignment,
            [
                'group_id' => $assignment->group_id,
                'unit_id' => $assignment->unit_id,
            ]
        );
    }
}
