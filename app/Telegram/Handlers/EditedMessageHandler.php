<?php

namespace App\Telegram\Handlers;

use App\Helpers\API\DeepSeek;
use App\Helpers\API\Telegram;
use App\Helpers\MarkdownToTelegramHtml;
use App\Models\Chat;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class EditedMessageHandler
{
    public function __construct(
        private DeepSeek $deepseek,
        private Telegram $telegram
    ) {
    }

    public function handle(array $editedMessage): void
    {
        $chatId = $editedMessage['chat']['id'] ?? null;
        $editedMessageId = $editedMessage['message_id'] ?? null;

        if (!$chatId || !$editedMessageId) {
            return;
        }

        $message = Message::where('message_id', $editedMessageId)->first();

        if (!$message) {
            return;
        }

        $messagesToDelete = Message::where('id', '>', $message->id)->get();

        foreach ($messagesToDelete as $msg) {
            if ($msg->message_id) {
                $this->telegram
                    ->setMethod('deleteMessage')
                    ->setParameters([
                        'chat_id'    => $chatId,
                        'message_id' => $msg->message_id,
                    ])
                    ->result();
            }

            if ($msg->stats_message_id) {
                $this->telegram
                    ->setMethod('deleteMessage')
                    ->setParameters([
                        'chat_id'    => $chatId,
                        'message_id' => $msg->stats_message_id,
                    ])
                    ->result();
            }
        }

        Message::where('id', '>', $message->id)->delete();

        if (isset($editedMessage['text'])) {
            $message->update(['message' => $editedMessage['text']]);
        }

        $this->deepseek->addMessage(file_get_contents(resource_path('system_message.txt')), 'system');

        $chat = Chat::where('chat_id', $chatId)->first();

        $messages = Message::forActiveCharacters($chat?->active_characters ?? [])
            ->orderBy('id')
            ->get();
        foreach ($messages as $msg) {
            $role = $msg->type === 'USER' ? 'user' : 'assistant';
            $this->deepseek->addMessage($msg->message, $role);
        }

        $result = $this->deepseek->result();

        if ($result) {
            $content = $result['choices'][0]['message']['content'];
            $deepseekId = $result['id'] ?? null;
            $sentMessageId = $this->sendChunk($chatId, mb_substr($content, 0, 3000), $deepseekId);

            $aiMessage = Message::create([
                'type'        => 'AI',
                'message_id'  => $sentMessageId,
                'deepseek_id' => $deepseekId,
                'message'     => $content,
                'witnesses'   => $chat->active_characters ?? [],
            ]);

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
                        'text'       => $usageText,
                        'chat_id'    => $chatId,
                        'parse_mode' => 'HTML',
                    ])
                    ->result();

                if (!empty($statsResult['result']['message_id'])) {
                    $aiMessage->update([
                        'stats_message_id' => $statsResult['result']['message_id'],
                    ]);
                }
            }
        } else {
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

        if (!$sendResult || empty($sendResult['ok'])) {
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
        }

        return $sendResult['result']['message_id'] ?? null;
    }
}
