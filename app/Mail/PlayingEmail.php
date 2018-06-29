<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PlayingEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $playerPlaying;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($playerPlaying)
    {
        $this->playerPlaying = $playerPlaying;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from('hb@chessageme.com')
        ->view('email.playingEmail');
    }
}
