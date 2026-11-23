<?php

namespace App\Telegram\Handlers;

use App\Models\Chat;
use App\Models\Message;
use App\Telegram\Commands\GetCharCommand;
use App\Telegram\Commands\SetCharAllCommand;
use App\Telegram\Commands\SetCharCommand;
use App\Telegram\Commands\StartCommand;
use App\Telegram\States\AuthState;
use App\Helpers\API\Telegram;
use App\Helpers\API\DeepSeek;
use App\Helpers\MarkdownToTelegramHtml;
use Illuminate\Support\Facades\Log;

class MessageHandler
{
    public function __construct(
        private DeepSeek $deepseek,
        private StartCommand $startCommand,
        private SetCharCommand $setCharCommand,
        private SetCharAllCommand $setCharAllCommand,
        private GetCharCommand $getCharCommand,
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

        if (str_starts_with($text, '/get_char')) {
            $this->getCharCommand->handle($chat, $message);
            return;
        }

        if (str_starts_with($text, '/set_char_all')) {
            $this->setCharAllCommand->handle($chat, $message);
            return;
        }

        if (str_starts_with($text, '/set_char')) {
            $this->setCharCommand->handle($chat, $message);
            return;
        }

        // Handle regular messages
        $lastAiMessage = Message::where('type', 'AI')
            ->whereNotNull('message_id')
            ->orderByDesc('id')
            ->first();

        if ($lastAiMessage) {
            $this->telegram
                ->setMethod('editMessageReplyMarkup')
                ->setParameters([
                    'chat_id'      => $chatId,
                    'message_id'   => $lastAiMessage->message_id,
                    'reply_markup' => json_encode(['inline_keyboard' => []]),
                ])
                ->result();
        }

        Message::create([
            'type'       => 'USER',
            'message_id' => $message['message_id'] ?? null,
            'message'    => $text,
            'witnesses'  => $chat->active_characters ?? [],
        ]);

        $this->deepseek->addMessage(file_get_contents(resource_path('system_message.txt')), 'system');

        $messages = Message::forActiveCharacters($chat->active_characters ?? [])
            ->orderBy('id')
            ->get();
        foreach ($messages as $msg) {
            $role = $msg->type === 'USER' ? 'user' : 'assistant';
            $this->deepseek->addMessage($msg->message, $role);
        }

        $result = $this->deepseek->result();

        if ($result) {
            $content = $result['choices'][0]['message']['content'];

            // Force cut message to 3000 characters (Telegram limit is 4096)
            $deepseekId = $result['id'] ?? null;
            $sentMessageId = $this->sendChunk($chatId, mb_substr($content, 0, 3000), $deepseekId);

            $aiMessage = Message::create([
                'type'        => 'AI',
                'message_id'  => $sentMessageId,
                'deepseek_id' => $deepseekId,
                'message'     => $content,
                'witnesses'   => $chat->active_characters ?? [],
            ]);

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


                $statsResult = $this->telegram
                    ->setMethod('sendMessage')
                    ->setParameters([
                        'text'         => $usageText,
                        'chat_id'      => $chatId,
                        'parse_mode'   => 'HTML',
                    ])
                    ->result();

                if (!empty($statsResult['result']['message_id'])) {
                    $aiMessage->update([
                        'stats_message_id' => $statsResult['result']['message_id'],
                    ]);
                }
            }

            return;
        }
        else {
            Log::error('DeepSeek error: ' . $this->deepseek->error);
        }
    }

    private function sendChunk(int|string $chatId, string $chunk, ?string $deepseekId = null): int|string|null
    {
        $replyMarkup = $deepseekId ? json_encode([
            'inline_keyboard' => [
                [
                    ['text' => '🔄', 'callback_data' => 'deepseek_update:' . $deepseekId],
                ],
            ],
        ]) : null;

        // Try sending as HTML first
        $params = [
            'text'       => MarkdownToTelegramHtml::convert($chunk),
            'chat_id'    => $chatId,
            'parse_mode' => 'HTML',
        ];
        if ($replyMarkup) {
            $params['reply_markup'] = $replyMarkup;
        }

        $sendResult = $this->telegram
            ->setMethod('sendMessage')
            ->setParameters($params)
            ->result();

        if ($sendResult) {
            return $sendResult['result']['message_id'] ?? null;
        }

        // Fallback: send as plain text if HTML parsing failed
        Log::warning('Telegram sendMessage (HTML) failed, falling back to plain text: ', (array) $this->telegram->error);

        $fallbackParams = [
            'text'    => $chunk,
            'chat_id' => $chatId,
        ];
        if ($replyMarkup) {
            $fallbackParams['reply_markup'] = $replyMarkup;
        }

        $sendResult = $this->telegram
            ->setMethod('sendMessage')
            ->setParameters($fallbackParams)
            ->result();

        if (!$sendResult) {
            Log::error('Telegram sendMessage error: ', (array) $this->telegram->error);
            return null;
        }

        return $sendResult['result']['message_id'] ?? null;
    }
}