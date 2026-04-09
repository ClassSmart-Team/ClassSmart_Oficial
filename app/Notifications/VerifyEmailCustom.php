<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

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

            // 👇 IMAGEN AQUÍ
            ->line(new HtmlString(
                '<div style="text-align:center; margin-bottom:20px;">
                    <img src="https://api.sutando-user.me/images/logo-mail.png" width="90" />
                </div>'
            ))

            ->line('Tu cuenta fue creada correctamente en ClassSmart.')
            ->line('Ya puedes iniciar sesion con tu correo y contraseña.')

            ->action('Ir a ClassSmart', config('app.url.frontend'));
    }
}