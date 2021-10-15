<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Validation\ValidationException;

class ResetPasswordNotification extends ResetPassword
{
    /**
     * @param mixed $notifiable
     * 
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $url = $this->getNotificationEndPoint($notifiable);
        return $this->buildMailMessage($url);
    }

    /**
     * @param mixed $notifiable
     * 
     * @return string
     */
    public function getNotificationEndPoint($notifiable)
    {
        if (!$endpoint = env('FRONT_END_URL')) {
            throw ValidationException::withMessages(['There is no domain set']);
        }

        return $endpoint . "?token={$this->token}&email={$notifiable->getEmailForPasswordReset()}";
    }
}
