<?php

namespace App\Telegram;

use App\Telegram\Handlers\MessageHandler;
use App\Telegram\Handlers\CallbackQueryHandler;

class TelegramRouter
{
    public function __construct(
        private MessageHandler $messageHandler,
        private CallbackQueryHandler $callbackQueryHandler
    )
    {

    }

    public function handle(array $update): void
    {
        // Handle message
        if (isset($update['message'])) {
            $this->messageHandler->handle($update['message']);
        }

        // Handle callback query
        if (isset($update['callback_query'])) {
            $this->messageHandler->handle($update['callback_query']);
        }
    }
}