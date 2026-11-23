<?php

namespace App\Telegram\Commands;

use App\Helpers\API\Telegram;
use App\Models\Chat;

class SetCharAllCommand
{
    public function __construct(
        private Telegram $telegram
    )
    {

    }

    public function handle(Chat $chat, array $message): void
    {
        $chat->update([
            'active_characters' => [],
        ]);

        $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'    => 'Active characters cleared.',
                'chat_id' => $message['chat']['id'],
            ])
            ->result();
    }
}
