<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\User;
use App\Notifications\Channels\FcmChannel;

class ReportCardNotification extends Notification
{
    use Queueable;

    protected $data;
    protected $student;

    public function __construct(User $student, array $data)
    {
        $this->student = $student;
        $this->data = $data;
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
        $isParent = ($notifiable->id !== $this->student->id);

        $view = $isParent ? 'emails.report_card_parent' : 'emails.report_card_student';
        $url = $isParent ? 'https://sutando-user.me/parent/grades' : 'https://sutando-user.me/student/grades';

        return (new MailMessage)
            ->subject('REPORTE ACADÉMICO: ' . mb_strtoupper($this->student->name, 'UTF-8'))
            ->view($view, [
                'parentName'  => $notifiable->name,
                'studentName' => $this->student->name,
                'average'     => $this->data['average'],
                'periodName'  => $this->data['periodName'],
                'unitName'    => $this->data['unitName'],
                'groupName'   => $this->data['groupName'],
                'url'         => $url
            ]);
    }

    public function toFcm($notifiable)
    {
        $isParent = ($notifiable->id !== $this->student->id);
        $url = $isParent ? 'https://sutando-user.me/parent/grades' : 'https://sutando-user.me/student/grades';

        $title = $isParent
            ? "Boleta de " . $this->student->name
            : "Tu boleta de calificaciones";

        $body = "Promedio: {$this->data['average']} en {$this->data['periodName']} ({$this->data['unitName']})";

        return [
            'notification' => [
                'title' => $isParent ? "Boleta de " . $this->student->name : "Tu reporte académico",
                'body'  => "Promedio: {$this->data['average']} - {$this->data['periodName']}",
            ],
            'data' => [
                'url' => $url,
            ]
        ];
    }
}
