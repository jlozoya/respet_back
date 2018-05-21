<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class RegistrationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * La url de confirmación para el nuevo usuario.
     *
     * @var string
     */
    public $confirmationLink;

    /**
     * Crea una instancia de notificación.
     *
     * @param  string  $confirmationLink
     * @return void
     */
    public function __construct($confirmationLink)
    {
        $this->confirmationLink = $confirmationLink;
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
            ->subject('Confirmación de correo')
            ->greeting('Hola!')
            ->line('Usted está recibiendo este correo porque se registró exitosamente su cuenta y queremos confirmar su correo. Haga clic en el botón a continuación para confirmar:')
            ->action('Confirmar correo', $this->confirmationLink)
            ->line('Si no solicitó registrarse, no se requieren mas acciones.')
            ->salutation('Saludos');
    }
}