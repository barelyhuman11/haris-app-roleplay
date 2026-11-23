<?php

namespace App\Telegram\Handlers;

use App\Helpers\API\DeepSeek;
use App\Helpers\API\Telegram;
use App\Helpers\MarkdownToTelegramHtml;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

class CallbackQueryHandler
{
    public function __construct(
        private DeepSeek $deepseek,
        private Telegram $telegram
    ) {
    }

    public function handle(array $callbackQuery): void
    {
        $data = $callbackQuery['data'] ?? '';

        if (str_starts_with($data, 'deepseek_update:')) {
            $deepseekId = substr($data, strlen('deepseek_update:'));

            if ($deepseekId === '') {
                return;
            }

            $message = Message::where('deepseek_id', $deepseekId)->first();

            if (!$message) {
                return;
            }

            if (!empty($callbackQuery['id'])) {
                $this->telegram
                    ->setMethod('answerCallbackQuery')
                    ->setParameters(['callback_query_id' => $callbackQuery['id']])
                    ->result();
            }

            $this->deepseek->addMessage(file_get_contents(resource_path('system_message.txt')), 'system');

            $chatId = $callbackQuery['message']['chat']['id'] ?? null;
            $chat = $chatId ? \App\Models\Chat::where('chat_id', $chatId)->first() : null;

            $messages = Message::where('id', '<', $message->id)
                ->forActiveCharacters($chat?->active_characters ?? [])
                ->orderBy('id')
                ->get();

            foreach ($messages as $msg) {
                $role = $msg->type === 'USER' ? 'user' : 'assistant';
                $this->deepseek->addMessage($msg->message, $role);
            }

            $result = $this->deepseek->result();

            if ($result) {
                $content = $result['choices'][0]['message']['content'];
                $newDeepseekId = $result['id'] ?? $deepseekId;

                $message->update([
                    'deepseek_id' => $newDeepseekId,
                    'message'     => $content,
                ]);

                $telegramMessageId = $callbackQuery['message']['message_id'] ?? $message->message_id;

                if ($chatId && $telegramMessageId) {
                    $this->telegram
                        ->setMethod('editMessageText')
                        ->setParameters([
                            'chat_id'      => $chatId,
                            'message_id'   => $telegramMessageId,
                            'text'         => MarkdownToTelegramHtml::convert(mb_substr($content, 0, 3000)),
                            'parse_mode'   => 'HTML',
                            'reply_markup' => json_encode([
                                'inline_keyboard' => [
                                    [
                                        ['text' => '🔄', 'callback_data' => 'deepseek_update:' . $newDeepseekId],
                                    ],
                                ],
                            ]),
                        ])
                        ->result();
                }

                if ($chatId && $message->stats_message_id && !empty($result['usage'])) {
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
                        ->setMethod('editMessageText')
                        ->setParameters([
                            'chat_id'    => $chatId,
                            'message_id' => $message->stats_message_id,
                            'text'       => $usageText,
                            'parse_mode' => 'HTML',
                        ])
                        ->result();
                }
            } else {
                Log::error('DeepSeek error: ' . $this->deepseek->error);
            }
        }
    }
}