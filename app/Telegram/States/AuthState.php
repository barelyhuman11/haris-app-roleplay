<?php

namespace App\Telegram\States;

use App\Models\Chat;
use App\Helpers\API\Telegram;

class AuthState
{
    public function __construct(
        private Telegram $telegram
    )
    {

    }

    private $password = 'Colossus1610';

    public function handle(Chat $chat, array $message): void
    {
        $text = $message['text'] ?? '';

        $isPasswordCorrect = false;

        if ($text == $this->password) {
            $chat->update([
                'is_auth'   => 'Y',
                'state'     => NULL,
            ]);

            $isPasswordCorrect = true;
        }

        $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'      => $isPasswordCorrect ? 'Authenticated ✅' : 'Wrong password ❌',
                'chat_id'   => $chat->chat_id,
            ])
            ->result();
    }
}