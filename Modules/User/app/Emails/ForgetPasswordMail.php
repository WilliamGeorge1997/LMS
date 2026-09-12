<?php

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $verifyCode;

    /**
     * Create a new message instance.
     */
    public function __construct($verifyCode)
    {
        $this->verifyCode = $verifyCode;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Password Reset Code')
            ->html("Your verification code is: <strong>{$this->verifyCode}</strong>");
    }
}
