<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessage extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * Es la información a enviar.
     *
     * @var object
     */
    private $contact;
    /**
     * Es el usuario al que se le envia el mensaje.
     *
     * @var object
     */
    private $user;

    /**
     * Crea una instancia de notificación.
     *
     * @param  string  $confirmationLink
     * @return void
     */
    public function __construct($contact, $user)
    {
        $this->contact = $contact;
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
            ->subject('Nuevo mensaje de contacto')
            ->greeting('Hola ' . $this->user['name'] . '!')
            ->line('Un usuario envió un mensaje de contacto')
            ->line('Nombre: ' . $this->contact['name'])
            ->line('Teléfono: ' . $this->contact['phone'])
            ->line('Email: ' . $this->contact['email'])
            ->line('Mensaje: ' . $this->contact['message'])
            ->salutation('Saludos');
    }
}