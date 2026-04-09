<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailCustom extends VerifyEmail
{
    protected function buildMailMessage($url)
    {
        return (new MailMessage)
            ->subject('Confirma tu cuenta en Estética Nova')
            ->greeting('Hola!')
            ->line('Gracias por registrarte en Estética Nova.')
            ->line('Para comenzar, necesitas confirmar tu correo electrónico.')
            ->action('Confirmar mi cuenta', $url)
            ->line('Si tú no creaste esta cuenta, ignora este correo.');
    }
}