<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\FcmChannel;
use App\Models\Assignment;
use App\Models\User;

class AssignmentNotification extends Notification
{
    use Queueable;

    protected $assignment;
    protected $student;

    /**
     * @param Assignment $assignment
     * @param User $student El alumno dueño de la tarea
     */
    public function __construct(Assignment $assignment, User $student)
    {
        $this->assignment = $assignment;
        $this->student = $student;
    }

    public function via($notifiable): array
    {
        $c = $notifiable->configuration;
        if (!$c) return [];

        $channels = [];
        if ($c->email_notification && $c->email_new_assignments) {
            $channels[] = 'mail';
        }
        if ($c->push_notification && $notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $isParent = $notifiable->id !== $this->student->id;

        $subject = $isParent
            ? 'NUEVA TAREA PARA ' . mb_strtoupper($this->student->name, 'UTF-8')
            : 'TIENES UNA NUEVA TAREA: ' . mb_strtoupper($this->assignment->title, 'UTF-8');

        $view = $isParent ? 'emails.assignment_parent_notification' : 'emails.new_assignment_notification';
        $url  = $isParent ? 'https://sutando-user.me/parent/assignments' : 'https://sutando-user.me/student/tasks';

        return (new MailMessage)
            ->subject($subject)
            ->view($view, [
                'parentName'      => $notifiable->name,
                'studentName'     => $this->student->name,
                'groupName'       => $this->assignment->group->name,
                'assignmentTitle' => $this->assignment->title,
                'dueDate'         => $this->assignment->end_date->format('d/m/Y H:i'),
                'url'             => $url,
            ]);
    }

    public function toFcm($notifiable)
    {
        $isParent = $notifiable->id !== $this->student->id;

        return [
            'notification' => [
                'title' => $isParent ? "Tarea para {$this->student->name}" : "Nueva tarea asignada",
                'body'  => "{$this->assignment->group->name}: {$this->assignment->title}",
                'icon'  => '/img/Logo.svg',
            ],
            'data' => [
                'url'   => $isParent ? "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}" : "https://sutando-user.me/student/tasks/{$this->assignment->id}"
            ]
        ];
    }
}
