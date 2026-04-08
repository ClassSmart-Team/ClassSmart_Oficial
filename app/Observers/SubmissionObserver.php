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
            $submission->student_id
        );
    }

    public function updated(Submission $submission): void
    {
        if (!$submission->wasChanged()) {
            return;
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
            $submission
        );
    }

    public function deleted(Submission $submission): void
    {
        AuditLogger::record(
            'submissions.deleted',
            "Entrega #{$submission->id} eliminada",
            $submission
        );
    }
}
