<?php

namespace Modules\Notification\Listeners;

use Illuminate\Queue\Events\JobFailed;
use Modules\Notification\Services\NotificationService;
use Modules\User\Emails\ForgetPasswordMail;
use Modules\User\Emails\RegisterMail;

class NotifyFailedJob
{
    public function handle(JobFailed $event): void
    {
        $allowed = [
            RegisterMail::class,
            ForgetPasswordMail::class,
        ];

        if (! in_array($event->job->resolveName(), $allowed)) {
            return;
        }

        NotificationService::sendTelegram(
            '⚠️ [Email Failed]',
            $event->job->resolveName()."\n".$event->exception->getMessage()
        );
    }
}
