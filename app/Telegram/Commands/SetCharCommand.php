<?php

namespace App\Telegram\Commands;

use App\Helpers\API\Telegram;
use App\Models\Chat;

class SetCharCommand
{
    public function __construct(
        private Telegram $telegram
    )
    {

    }

    public function handle(Chat $chat, array $message): void
    {
        $text = trim($message['text'] ?? '');
        $args = preg_replace('/^\/set_char(?:@\S+)?\s*/', '', $text);
        $characters = $args === '' ? [] : preg_split('/\s+/', $args, -1, PREG_SPLIT_NO_EMPTY);

        $chat->update([
            'active_characters' => $characters,
        ]);

        $replyText = empty($characters)
            ? 'Active characters cleared.'
            : 'Active characters updated: ' . implode(', ', $characters);

        $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'    => $replyText,
                'chat_id' => $message['chat']['id'],
            ])
            ->result();
    }
}
