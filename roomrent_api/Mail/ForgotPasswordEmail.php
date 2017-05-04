<?php

namespace Roomrent\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Roomrent\User\Models\User;

class ForgotPasswordEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Stores named url
     * @var String
     */
    public $url;

    /**
     * Object that binds User Class
     * @var Object
     */ 
    public $user;
    
    /**
     * Constructor
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->url = route('user.forgotpassword', $user->forgot_token);
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.forgotPasswordEmail');
    }
}
