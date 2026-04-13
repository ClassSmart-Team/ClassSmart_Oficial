<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Notifications\Channels\FcmChannel;
use App\Models\Announcement;
use App\Models\User;

class AnnouncementNotification extends Notification
{
    use Queueable;

    protected $announcement;
    protected $student;

    /**
     * @param Announcement $announcement
     * @param User|null $student El alumno al que pertenece el anuncio (necesario para la vista del padre)
     */
    public function __construct(Announcement $announcement, User $student = null)
    {
        $this->announcement = $announcement;
        $this->student = $student;
    }

    public function via($notifiable): array
    {
        $c = $notifiable->configuration;
        if (!$c) return [];

        $channels = [];
        if ($c->email_notification && $c->email_new_announcements) {
            $channels[] = 'mail';
        }
        if ($c->push_notification && $notifiable->fcm_token) {
            $channels[] = FcmChannel::class;
        }
        return $channels;
    }

    public function toMail($notifiable): MailMessage
    {
        $isParent = $this->student && ($notifiable->id !== $this->student->id);

        $subject = $isParent
            ? 'AVISO ESCOLAR PARA: ' . mb_strtoupper($this->student->name, 'UTF-8')
            : 'NUEVO ANUNCIO EN ' . mb_strtoupper($this->announcement->group->name, 'UTF-8');

        $view = $isParent ? 'emails.new_announcement_parent' : 'emails.new_announcement_student';
        $url  = $isParent ? "https://sutando-user.me/parent/announcements/{$this->announcement->group->id}" : "https://sutando-user.me/student/announcements/{$this->announcement->id}";

        return (new MailMessage)
            ->subject($subject)
            ->view($view, [
                'parentName'  => $notifiable->name,
                'studentName' => $this->student?->name,
                'userName'    => $notifiable->name,
                'groupName'   => $this->announcement->group->name,
                'title'       => $this->announcement->title,
                'content'     => $this->announcement->content,
                'url'         => $url,
            ]);
    }

    public function toFcm($notifiable)
    {
        $isParent = $this->student && ($notifiable->id !== $this->student->id);

        return [
            'notification' => [
                'title' => $isParent
                    ? "Aviso de {$this->student->name}"
                    : "Nuevo anuncio: {$this->announcement->group->name}",
                'body'  => $this->announcement->title,
            ],
            'data' => [
                'url' => $isParent
                    ? "https://sutando-user.me/parent/announcements/{$this->announcement->group->id}"
                    : "https://sutando-user.me/student/announcements/{$this->announcement->id}"
            ]
        ];
    }
}
