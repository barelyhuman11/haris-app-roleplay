<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use App\Models\Message;
use App\Telegram\Commands\StartCommand;
use App\Telegram\States\AuthState;
use App\Helpers\API\Telegram;
use App\Helpers\API\DeepSeek;
use App\Helpers\MarkdownToTelegramHtml;
use App\Helpers\MarkdownSplitter;
use Illuminate\Support\Facades\Log;

class MessageHandler
{
    public function __construct(
        private DeepSeek $deepseek,
        private StartCommand $startCommand,
        private AuthState $authState,
        private Telegram $telegram
    )
    {

    }

    public function handle(array $message): void
    {
        $chatId = $message['chat']['id'];
        $chat = Chat::firstOrCreate(
            ['chat_id' => $chatId],
            ['is_auth' => 'N']
        );

        // Handle state
        if (!empty($chat->state)) {
            
            if ($chat->state == 'auth') {
                $this->authState->handle($chat, $message);
                return;
            }

        }
        // [END] Handle state

        // Handle authentication
        if ($chat->is_auth == 'N') {
            $this->telegram
                ->setMethod('sendMessage')
                ->setParameters([
                    'text'      => 'Enter the password',
                    'chat_id'   => $chatId,
                ])
                ->result();
            $chat->update(['state' => 'auth']);
            return;
        }
        // [END] Handle authentication

        $text = $message['text'] ?? '';

        // Handle commands
        if (str_starts_with($text, '/start')) {
            $this->startCommand->handle($message);
            return;
        }

        // Handle regular messages
        Message::create([
            'type'       => 'USER',
            'message_id' => $message['message_id'] ?? null,
            'message'    => $text,
        ]);

        $messages = Message::orderBy('id')->get();
        foreach ($messages as $msg) {
            $role = $msg->type === 'USER' ? 'user' : 'assistant';
            $this->deepseek->addMessage($msg->message, $role);
        }

        $result = $this->deepseek->result();

        if ($result) {
            $content = $result['choices'][0]['message']['content'];

            Message::create([
                'type'       => 'AI',
                'message_id' => $result['id'] ?? null,
                'message'    => $content,
            ]);

            // Split into chunks that fit Telegram's 4096 character limit
            foreach (MarkdownSplitter::split($content) as $chunk) {
                $this->sendChunk($chatId, $chunk);
            }

            // Send AI usage stats
            if (!empty($result['usage'])) {
                $usage = $result['usage'];
                $totalTokens = $usage['total_tokens'] ?? 0;
                $cachedTokens = $usage['prompt_tokens_details']['cached_tokens'] ?? 0;
                $promptCacheHitTokens = $usage['prompt_cache_hit_tokens'] ?? 0;
                $promptCacheMissTokens = $usage['prompt_cache_miss_tokens'] ?? 0;

                $usageText = "📊 <b>AI Usage:</b>\n"
                    . "• Total tokens: {$totalTokens}\n"
                    . "• Cached tokens: {$cachedTokens}\n"
                    . "• Prompt cache hit tokens: {$promptCacheHitTokens}\n"
                    . "• Prompt cache miss tokens: {$promptCacheMissTokens}";

                $this->telegram
                    ->setMethod('sendMessage')
                    ->setParameters([
                        'text'       => $usageText,
                        'chat_id'    => $chatId,
                        'parse_mode' => 'HTML',
                    ])
                    ->result();
            }

            return;
        }
        else {
            Log::error('DeepSeek error: ' . $this->deepseek->error);
        }
    }

    private function sendChunk(int|string $chatId, string $chunk): void
    {
        // Try sending as HTML first
        $sendResult = $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'       => MarkdownToTelegramHtml::convert($chunk),
                'chat_id'    => $chatId,
                'parse_mode' => 'HTML',
            ])
            ->result();

        if ($sendResult) {
            return;
        }

        // Fallback: send as plain text if HTML parsing failed
        Log::warning('Telegram sendMessage (HTML) failed, falling back to plain text: ', (array) $this->telegram->error);

        $sendResult = $this->telegram
            ->setMethod('sendMessage')
            ->setParameters([
                'text'    => $chunk,
                'chat_id' => $chatId,
            ])
            ->result();

        if (!$sendResult) {
            Log::error('Telegram sendMessage error: ', (array) $this->telegram->error);
        }
    }
}