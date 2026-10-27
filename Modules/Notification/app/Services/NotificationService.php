<?php

namespace Modules\Notification\Services;

use NotificationChannels\Telegram\TelegramMessage;

class NotificationService
{
    public static function sendTelegram(string $title, string $details): void
    {
        try {
            $chatId = config('services.telegram.chat_id');

            if (! $chatId) {
                return;
            }

            TelegramMessage::create()
                ->to($chatId)
                ->parseMode('')
                ->line($title)
                ->line($details)
                ->line('Time: '.now()->toDateTimeString())
                ->send();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Telegram notification error: '.$e->getMessage());
        }
    }
}
