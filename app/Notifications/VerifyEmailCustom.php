<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailCustom extends Notification
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenido a ClassSmart')
            ->greeting('Hola '.$notifiable->name.'!')
            ->line('Tu cuenta fue creada correctamente en ClassSmart.')
            ->line('Ya puedes iniciar sesion con tu correo y contraseña.')
            ->action('Ir a ClassSmart', config('app.url'));
    }
}