<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EnquiryReplay extends Mailable
{
    use Queueable, SerializesModels;

    public $replay;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($replay)
    {
        $this->replay = $replay;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Enquiry replay')->view('enquiry_replay');
    }
}
