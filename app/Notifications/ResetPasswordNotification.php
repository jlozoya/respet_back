<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * La url de restablecimiento de contraseña.
     *
     * @var string
     */
    private $resetLink;

    /**
     * Crea una instancia de notificación.
     *
     * @param  string  $resetLink
     * @return void
     */
    public function __construct($resetLink)
    {
        $this->resetLink = $resetLink;
    }

    /**
     * Obtiene los canales de notificación.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Recibe el mensaje de notificación.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\MessageBuilder
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Recuperar contraseña')
            ->greeting('Hola!')
            ->line('Usted está recibiendo este correo electrónico porque recibimos una solicitud para reestablecer la contraseña de su cuenta. Haga clic en el botón a continuación para restablecer su contraseña:')
            ->action('Restablecer la contraseña', $this->resetLink)
            ->line('Si no solicitó restablecer la contraseña, no se requieren más acciones.')
            ->salutation('Saludos');
    }
}