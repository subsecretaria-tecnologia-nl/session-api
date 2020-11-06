<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;

class NotaryNotification extends Notification
{
    public $username;
    public $password;

    public function __construct($user, $username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

   
    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject(Lang::get('Prueba'))
            ->line(Lang::get('Tu cuenta ha sido registrada como usuario de notario.'))
            ->line(Lang::get('Estas son las siguientes claves, usuario:'. $this->username.' '.'password:'. $this->password));
    }


}
