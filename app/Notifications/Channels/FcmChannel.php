<?php

namespace App\Notifications\Channels;

use Exception;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class FcmChannel
{
    protected $messaging;

    public function __construct(Messaging $messaging)
    {
        $this->messaging = $messaging;
    }

    public function send($notifiable, Notification $notification)
    {
        $token = $notifiable->fcm_token;

        if (!$token) {
            return;
        }

        $payload = $notification->toFcm($notifiable);

        $title = $payload['notification']['title'] ?? 'ClassSmart';
        $body  = $payload['notification']['body'] ?? '';
        $url   = $payload['data']['url'] ?? '/';

        $message = CloudMessage::fromArray([
            'token' => $token,
            'data' => [
                'title' => (string)$title,
                'body'  => (string)$body,
                'url'   => (string)$url,
                'icon'  => '/Logo.svg',
            ],
        ]);

        try {
            $this->messaging->send($message);
        } catch (Exception $e) {
            Log::error("Error enviando FCM en ClassSmart: " . $e->getMessage());
        }
    }
}
