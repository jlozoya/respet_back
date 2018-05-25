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
    private $confirmationLink;
    /**
     * Idioma para mandar el correo.
     * @var string
     */
    private $lang;
    /**
     * Crea una instancia de notificación.
     *
     * @param  string  $confirmationLink
     * @return void
     */
    public function __construct($confirmationLink, $lang = 'es')
    {
        $this->confirmationLink = $confirmationLink;
        $this->lang = $lang;
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
        if ($this->lang == 'es') {
            return (new MailMessage)
                ->subject('Confirmación de correo')
                ->greeting('Hola!')
                ->line('Usted está recibiendo este correo porque se registró exitosamente su cuenta y queremos confirmar su correo. Haga clic en el botón a continuación para confirmar:')
                ->action('Confirmar correo', $this->confirmationLink)
                ->line('Si no solicitó registrarse, no se requieren mas acciones.')
                ->salutation('Saludos');
        } else if ($this->lang == 'en') {
            return (new MailMessage)
                ->subject('Email confirmation')
                ->greeting('Hello!')
                ->line('You are receiving this email because your account was successfully registered and we want to confirm your email. Click on the button below to confirm:')
                ->action('Confirm email', $this->confirmationLink)
                ->line('If you did not request registration, no further action is required.')
                ->salutation('Regards');
        }
    }
}