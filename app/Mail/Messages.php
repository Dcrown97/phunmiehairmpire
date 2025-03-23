<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Messages extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailData)
    {
        $this->mailData = $mailData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = 'Phunmiehairmpire';
        $address = env('MAIL_FROM_ADDRESS');
        $to = $this->mailData['email'];
        $subject = 'New Message from Phunmiehairmpire';
        return $this->view('mail.message')
            ->from($address, $name)
            ->to($to) // Ensure recipient is set
            ->subject($subject)
            ->with([
                'mailData' => $this->mailData,
            ]);
    }
}
