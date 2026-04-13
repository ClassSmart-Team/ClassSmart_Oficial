<?php

namespace App\Providers;

use App\Notifications\Channels\FcmChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Notification::extend('fcm', function ($app) {
            return new FcmChannel();
        });
    }

    public function sendPush($notifiable)
    {
        $data = $this->toFcm($notifiable);
        $message = CloudMessage::withTarget('token', $notifiable->fcm_token)
            ->withNotification(FirebaseNotification::create($data['title'], $data['body']))
            ->withData($data['data']);

        Firebase::messaging()->send($message);
    }
}
