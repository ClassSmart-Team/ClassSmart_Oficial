<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\FcmChannel;
use App\Models\Assignment;
use App\Models\User;

class SubmissionNotification extends Notification
{
    use Queueable;

    protected $assignment;
    protected $student;
    protected $submission;

    public function __construct(Assignment $assignment, User $student, $submission)
    {
        $this->assignment = $assignment;
        $this->student = $student;
        $this->submission = $submission;
    }

    public function via($notifiable): array
    {
        $c = $notifiable->configuration;
        if (!$c) return [];

        $channels = [];
        if ($c->email_notification) {
            $channels[] = 'mail';
        }

        if ($c->push_notification && $notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $isTeacher = $notifiable->isTeacher();

        $subject = $isTeacher
            ? 'NUEVA ENTREGA DE: ' . $this->student->name
            : 'TAREA ENTREGADA POR: ' . $this->student->name;

        $view = $isTeacher ? 'emails.submission_teacher' : 'emails.submission_parent';

        $url = $isTeacher
            ? "https://sutando-user.me/teacher/task/" . $this->assignment->id
            : "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}";

        return (new MailMessage)
            ->subject(mb_strtoupper($subject, 'UTF-8'))
            ->view($view, [
                'teacherName'     => $notifiable->name,
                'parentName'      => $notifiable->name,
                'studentName'     => $this->student->name,
                'assignmentTitle' => $this->assignment->title,
                'groupName'       => $this->assignment->group->name,
                'status'          => $this->submission->status,
                'isLate'          => $this->submission->status === 'Entregada tarde',
                'url'             => $url
            ]);
    }

    public function toFcm($notifiable)
    {
        $isTeacher = $notifiable->isTeacher();

        $url = $isTeacher
            ? "https://sutando-user.me/teacher/task/" . $this->assignment->id
            : "https://sutando-user.me/parent/assignments/{$this->assignment->id}/{$this->student->id}";

        return [
            'notification' => [
                'title' => $isTeacher ? "Nueva entrega recibida" : "Tarea entregada",
                'body'  => "{$this->student->name} ha entregado: {$this->assignment->title}",
            ],
            'data' => [
                'url' => $url,
            ]
        ];
    }
}
