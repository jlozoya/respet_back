<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Crea una instancia de notificación.
     *
     * @param  string  $confirmationLink
     * @return void
     */
    public function __construct()
    {
        
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
            ->subject('Confirmación de contacto')
            ->greeting('Hola!')
            ->line('Muchas gracias por ponerse en contacto con nosotros.')
            ->line('Si no fue usted, no se requieren mas acciones.')
            ->salutation('Saludos');
    }
}