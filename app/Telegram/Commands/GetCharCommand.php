<?php

namespace App\Telegram\Commands;

use App\Helpers\API\Telegram;
use App\Models\Chat;

class GetCharCommand
{
    public function __construct(
        private Telegram $telegram
    )
    {

    }

    public function handle(Chat $chat, array $message): void
    {
        $characters = $chat->active_characters ?? [];

        $replyText = empty($characters)
            ? 'No active characters set.'
            : 'Active characters: ' . implode(', ', $characters);

        $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'    => $replyText,
                'chat_id' => $message['chat']['id'],
            ])
            ->result();
    }
}
