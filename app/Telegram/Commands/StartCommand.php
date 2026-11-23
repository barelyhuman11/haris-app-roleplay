<?php

namespace App\Telegram\Commands;

use App\Helpers\API\Telegram;

class StartCommand
{
    public function __construct(
        private Telegram $telegram
    )
    {

    }

    public function handle(array $message): void
    {
        $sendMessage = $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'      => 'Hello, World!',
                'chat_id'   => $message['chat']['id'],
            ])
            ->result();
    }
}