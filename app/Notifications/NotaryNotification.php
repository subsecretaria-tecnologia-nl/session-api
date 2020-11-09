<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\HtmlString;

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
        $content = "<p>Claves de acceso:</p><p>Correo:".$notifiable->email."</p><p>Contraseña:".$this->password."</p>";
        $template = file_get_contents(getenv("PORTAL_HOSTNAME")."/email/template");
        $template = str_replace(["#_EMAIL_PREHEADER_#"], "Usuario Registrado Notaria", $template);
        $template = str_replace(["#_EMAIL_HEADER_#"], "Tu cuenta ha sido registrada como usuario de notrario", $template);
        $template = str_replace(["#_EMAIL_CONTENT_#"], $content, $template);

        return (new MailMessage)
        ->line(new HtmlString($template));
    }


}
