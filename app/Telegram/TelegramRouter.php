<?php

namespace App\Telegram;

use App\Telegram\Handlers\MessageHandler;
use App\Telegram\Handlers\EditedMessageHandler;
use App\Telegram\Handlers\CallbackQueryHandler;

class TelegramRouter
{
    public function __construct(
        private MessageHandler $messageHandler,
        private EditedMessageHandler $editedMessageHandler,
        private CallbackQueryHandler $callbackQueryHandler
    )
    {

    }

    public function handle(array $update): void
    {
        // Handle message
        if (isset($update['message'])) {
            $this->messageHandler->handle($update['message']);
            return;
        }

        // Handle edited message
        if (isset($update['edited_message'])) {
            $this->editedMessageHandler->handle($update['edited_message']);
            return;
        }

        // Handle callback query
        if (isset($update['callback_query'])) {
            $this->callbackQueryHandler->handle($update['callback_query']);
            return;
        }
    }
}