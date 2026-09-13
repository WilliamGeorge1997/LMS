<?php

declare(strict_types=1);

namespace Modules\User\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RegisterMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public string|int $verifyCode) {}

    /**
     * Build the message.
     */
    public function build(): self
    {
        $appName = tenant('name') ?? tenant()?->name ?? 'LMS';

        return $this->subject("Welcome to {$appName} - Verify your email")
            ->view('user::emails.register')
            ->text('user::emails.register-text')
            ->with([
                'verifyCode' => $this->verifyCode,
            ]);
    }
}
