<?php

namespace App\Console\Commands\Telegram;

use Illuminate\Console\Command;
use App\Helpers\API\Telegram as TelegramAPI;

class SetWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:set-webhook';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $Telegram = new TelegramAPI();

        $webhookUrl = route('telegram-webhook');

        $result = $Telegram
            ->setMethod('setWebhook')
            ->setParameters([
                'url' => $webhookUrl,
            ])
            ->result();

        if ($result) {
            $getWebhookInfo = $Telegram
                ->setMethod('getWebhookInfo')
                ->result();

            if ($getWebhookInfo && !empty($getWebhookInfo['result']) && $getWebhookInfo['result']['url'] == $webhookUrl) {
                $this->info('[ * ] Webhook set!');
            }
            else {
                $this->error('[ * ] ' . $Telegram->error);
            }
        }
        else {
            $this->error('[ * ] Webhook URL : ' . $webhookUrl);
            $this->error('[ * ] ' . $Telegram->error);
        }
    }
}
