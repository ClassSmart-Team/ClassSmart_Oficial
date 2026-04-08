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
            $assignment
        );
    }

    public function updated(Assignment $assignment): void
    {
        if (!$assignment->wasChanged()) {
            return;
        }

        AuditLogger::record(
            'assignments.updated',
            "Tarea editada: {$assignment->title}",
            $assignment
        );
    }

    public function deleted(Assignment $assignment): void
    {
        AuditLogger::record(
            'assignments.deleted',
            "Tarea eliminada: {$assignment->title}",
            $assignment
        );
    }
}
