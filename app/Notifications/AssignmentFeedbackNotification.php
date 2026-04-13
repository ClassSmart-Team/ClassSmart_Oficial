<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\FcmChannel;
use App\Models\Assignment;
use App\Models\User;

class AssignmentFeedbackNotification extends Notification
{
    use Queueable;

    protected $assignment;
    protected $student;
    protected $feedback;

    public function __construct(Assignment $assignment, User $student, $feedback)
    {
        $this->assignment = $assignment;
        $this->student = $student;
        $this->feedback = $feedback;
    }

    public function via($notifiable): array
    {
        $c = $notifiable->configuration;
        if (!$c) return [];

        $channels = [];

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

        $view = $isParent ? 'emails.feedback_parent' : 'emails.feedback_student';
        $url = $isParent ? "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}" : "https://sutando-user.me/student/tasks/{$this->assignment->id}";

        return (new MailMessage)
            ->subject('NUEVA RETROALIMENTACION EN: ' . mb_strtoupper($this->assignment->title, 'UTF-8'))
            ->view($view, [
                'parentName'      => $notifiable->name,
                'studentName'     => $this->student->name,
                'groupName'       => $this->assignment->group->name,
                'assignmentTitle' => $this->assignment->title,
                'feedback'        => $this->feedback,
                'url'             => $url
            ]);
    }

    public function toFcm($notifiable)
    {
        $isParent = $notifiable->id !== $this->student->id;

        $url = $isParent ? "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}" : "https://sutando-user.me/student/tasks/{$this->assignment->id}";
        return [
            'notification' => [
                'title' => 'Retroalimentación disponible',
                'body'  => $isParent
                    ? "El profesor comentó la tarea de {$this->student->name}"
                    : "El profesor ha calificado tu tarea: {$this->assignment->title}",
            ],
            'data' => [
                'url' => $url,
            ]
        ];


    }
}
