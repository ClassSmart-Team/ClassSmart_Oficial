<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\FcmChannel;
use App\Models\Assignment;
use App\Models\User;

class AssignmentGradedNotification extends Notification
{
    use Queueable;

    protected $assignment;
    protected $student;
    protected $grade;
    protected $feedback;

    public function __construct(Assignment $assignment, User $student, $grade, $feedback = null)
    {
        $this->assignment = $assignment;
        $this->student = $student;
        $this->grade = $grade;
        $this->feedback = $feedback;
    }

    public function via($notifiable): array
    {
        $c = $notifiable->configuration;
        if (!$c) return [];

        $channels = [];
        // Asumimos que usas los mismos switches de 'email_grades' para ambos
        if ($c->email_notification && $c->email_grades) {
            $channels[] = 'mail';
        }
        if ($c->push_notification && $notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $isParent = ($notifiable->id !== $this->student->id);

        $view = $isParent ? 'emails.grade_updated_parent' : 'emails.grade_updated_student';
        $url = $isParent ? "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}" : "https://sutando-user.me/student/tasks/{$this->assignment->id}";

        return (new MailMessage)
            ->subject('Calificación Registrada: ' . mb_strtoupper($this->assignment->title, 'UTF-8'))
            ->view($view, [
                'parentName'      => $notifiable->name,
                'studentName'     => $this->student->name,
                'groupName'       => $this->assignment->group->name,
                'assignmentTitle' => $this->assignment->title,
                'grade'           => $this->grade,
                'url'             => $url
            ]);
    }

    public function toFcm($notifiable)
    {
        $isParent = ($notifiable->id !== $this->student->id);

        $url = $isParent ? "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}" : "https://sutando-user.me/student/tasks/{$this->assignment->id}";

        return [
            'notification' => [
                'title' => $isParent ? "Nota de {$this->student->name}" : "Tarea calificada",
                'body'  => "{$this->assignment->title}: Nota {$this->grade}",
            ],
            'data' => [
                'url' => $url,
                'type' => 'grade_update'
            ]
        ];
    }
}
