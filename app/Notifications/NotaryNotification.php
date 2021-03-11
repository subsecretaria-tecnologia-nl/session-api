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
    protected $table;

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
        $content = "<h4>Estos son tus datos de acceso.</h4><p>Recuerda que necesitarás de ellos para poder acceder a tu cuenta en la plataforma unificada de trámites del Gobierno del Estado de Nuevo León.</p><br><div class=\"text-center\" style=\"margin-bottom: 40px;\"><p style=\"margin-bottom: 0;\">Correo</p><h1>{$notifiable->email}</h1><p style=\"margin-bottom: 0; margin-top: 10px;\">Contraseña</p><h1>{$this->password}</h1></div><p>Si tienes algún problema con tu acceso o necesitas más ayuda comunicate con tu administrador de cuentas para poder solucionar todos los detalles.</p>";
        // $content = "<p>Claves de acceso:</p><p>Correo:".$notifiable->email."</p><p>Contraseña:".$this->password."</p>";
        $template = file_get_contents(getenv("PORTAL_HOSTNAME")."/email/template");
        $template = str_replace(["#_EMAIL_PREHEADER_#"], "Tu cuenta ha sido registrada con éxito. Revisa tus credenciales para poder acceder a tu cuenta.", $template);
        $template = str_replace(["#_EMAIL_HEADER_#"], "Tu cuenta ha sido registrada con éxito.", $template);
        $template = str_replace(["#_EMAIL_CONTENT_#"], $content, $template);

        return (new MailMessage)
        ->line(new HtmlString($template)); 
    }


}
