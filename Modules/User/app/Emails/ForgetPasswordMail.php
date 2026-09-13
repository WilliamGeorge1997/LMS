<?php

declare(strict_types=1);

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgetPasswordMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string|int $verifyCode) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this->subject('Password Reset Code')
            ->view('user::emails.forget-password')
            ->text('user::emails.forget-password-text')
            ->with([
                'verifyCode' => $this->verifyCode,
            ]);
    }
}
