<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class ContactFormSubmitted extends Notification
{
    use Queueable;

    private $name;
    private $email;
    private $text;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($name, $email, $text)
    {
        $this->name = $name;
        $this->email = $email;
        $this->text = $text;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        return (new SlackMessage())
            ->from('Laravel Contact form', ':speech_balloon:')
            ->to('#laravel-remote-contact')
            ->content("New contact form submission from *$this->name <$this->email>*:\n\n>"
                . str_replace("\n", "\n>", $this->text));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
