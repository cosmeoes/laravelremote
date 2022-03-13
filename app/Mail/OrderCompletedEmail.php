<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderCompletedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public $order) {}

    public function build()
    {
        return $this->view('email.order-completed')
            ->to($this->order->email)
            ->from(...config('mail.from'))
            ->subject("Your Laravel Remote Job Post is now live!");
    }
}
