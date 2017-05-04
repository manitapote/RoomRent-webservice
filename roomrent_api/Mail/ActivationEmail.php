<?php

namespace Roomrent\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Roomrent\User\Models\User;

class ActivationEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Object to store user
     * @var [type]
     */
    public $user;

    /**
     * Stores named url
     * @var [type]
     */
    public $url;

    /**
     * Constructer
     * @param User $user 
     */ 
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->url = route('user.activate', $user->activation_token);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.activationEmail');
    }
}
