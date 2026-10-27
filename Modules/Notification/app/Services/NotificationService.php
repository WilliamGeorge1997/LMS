<?php

namespace Modules\Notification\Services;

use NotificationChannels\Telegram\TelegramMessage;

class NotificationService
{
    public static function sendTelegram(string $title, string $details): void
    {
        $chatId = config('services.telegram.chat_id');

        if (! $chatId) {
            return;
        }

        TelegramMessage::create()
            ->to($chatId)
            ->line($title)
            ->line($details)
            ->line('Time: '.now()->toDateTimeString())
            ->send();
    }
}
