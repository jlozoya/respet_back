<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;

class PayConfirmation extends Notification implements ShouldQueue
{
    use Queueable;
    /**
     * Idioma para mandar el correo.
     * @var string
     */
    private $course;
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
    public function __construct($course, $lang = 'es')
    {
        $this->course = $course;
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
                    ->subject('Confirmación de pago')
                    ->greeting('Hola!')
                    ->line('Le agracemos muchísimo por haber realizado el pago de inscripción, empezamos el día:')
                    ->line($this->$course['start_date'])
                    ->line('Lo mantendremos informado.')
                    ->salutation('Saludos');
            }
            break;
            case 'en': {
                return (new MailMessage)
                    ->subject('Confirmation of payment')
                    ->greeting('Hello!')
                    ->line('We thank you very much for having made the registration payment, we started the day:')
                    ->line($this->$course['start_date'])
                    ->line('We will keep you informed.')
                    ->salutation('Regards');
            }
            break;
            default: {
                return (new MailMessage)
                    ->subject('Confirmación de pago')
                    ->greeting('Hola!')
                    ->line('Le agracemos muchísimo por haber realizado el pago de inscripción, empezamos el día:')
                    ->line($this->$course['start_date'])
                    ->line('Lo mantendremos informado.')
                    ->salutation('Saludos');
            }
            break;
        }
    }
}