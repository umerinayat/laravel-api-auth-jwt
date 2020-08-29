<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Auth\Notifications\VerifyEmail as NotificationsVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends NotificationsVerifyEmail
{

    protected function verificationUrl( $notifiable )
    {
        $clientAppUrl = config( 'app.client_url', config('app.url') );

        $url = URL::temporarySignedRoute(
            'verification.verify', 
            Carbon::now()->addMinute(60),
            [
                'user' => $notifiable->id
            ]
        );

        return str_replace( url('/api'), $clientAppUrl, $url );
    }
}
