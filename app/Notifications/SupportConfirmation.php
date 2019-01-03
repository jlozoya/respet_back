<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class SupportConfirmation extends Notification implements ShouldQueue
{
    use Queueable;
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
    public function __construct($lang = 'es')
    {
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
        switch($this->lang) {
            case 'es': {
                return (new MailMessage)
                    ->subject('Confirmación de soporte')
                    ->greeting('Hola!')
                    ->line('Muchas gracias por ponerse en contacto con nosotros.')
                    ->line('Si no fue usted, no se requieren mas acciones.')
                    ->salutation('Saludos');
            }
            break;
            case 'en': {
                return (new MailMessage)
                    ->subject('Support confirmation')
                    ->greeting('Hello!')
                    ->line('Thank you very much for contacting us.')
                    ->line('If it was not you, no more actions are required.')
                    ->salutation ('Greetings');
            }
            break;
            default: {
                return (new MailMessage)
                    ->subject('Confirmación de contacto')
                    ->greeting('Hola!')
                    ->line('Muchas gracias por ponerse en contacto con nosotros.')
                    ->line('Si no fue usted, no se requieren mas acciones.')
                    ->salutation('Saludos');
            }
            break;
        }
    }
}