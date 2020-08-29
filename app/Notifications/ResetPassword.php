<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as NotificationsResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class ResetPassword extends NotificationsResetPassword
{
    use Queueable;

    public function toMail($notifiable)
    {
        $clientUrl = url(config('app.client_url') . '/password/reset/' . $this->token). 
                    '?email='.urldecode($notifiable->email);

        return (new MailMessage)
                    ->line('You are receving this email because we recevied a password reset requeset for your account.')
                    ->action('Reset Password',   $clientUrl)
                    ->line('If You did not request a password reset, no further action is required.');
    }

}
