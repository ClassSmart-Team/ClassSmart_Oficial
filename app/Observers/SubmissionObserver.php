<?php

namespace App\Observers;

use App\Models\Submission;
use App\Services\AuditLogger;

class SubmissionObserver
{
    public function created(Submission $submission): void
    {
        AuditLogger::record(
            'submissions.created',
            "Entrega registrada para assignment_id {$submission->assignment_id}",
            $submission,
            [
                'assignment_id' => $submission->assignment_id,
                'student_id' => $submission->student_id,
                'status' => $submission->status,
            ],
            $submission->student_id
        );
    }

    public function updated(Submission $submission): void
    {
        if (!$submission->wasChanged()) {
            return;
        }

        $changes = $submission->getChanges();
        unset($changes['updated_at']);

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        foreach (array_keys($changes) as $field) {
            $oldValues[$field] = $submission->getOriginal($field);
        }

        $action = 'submissions.updated';
        $description = "Entrega #{$submission->id} actualizada";

        if ($submission->wasChanged('grade') || $submission->wasChanged('feedback') || ($submission->wasChanged('status') && $submission->status === 'Calificada')) {
            $action = 'submissions.graded';
            $description = "Entrega #{$submission->id} calificada";
        }

        AuditLogger::record(
            $action,
            $description,
            $submission,
            [
                'old' => $oldValues,
                'new' => $changes,
                'assignment_id' => $submission->assignment_id,
                'student_id' => $submission->student_id,
            ]
        );
    }

    public function deleted(Submission $submission): void
    {
        AuditLogger::record(
            'submissions.deleted',
            "Entrega #{$submission->id} eliminada",
            $submission,
            [
                'assignment_id' => $submission->assignment_id,
                'student_id' => $submission->student_id,
            ]
        );
    }
}
