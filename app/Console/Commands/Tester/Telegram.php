<?php

namespace App\Console\Commands\Tester;

use Illuminate\Console\Command;
use App\Helpers\API\Telegram as TelegramAPI;

class Telegram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:telegram';

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
        $result = $Telegram
            ->setMethod('getMe')
            ->result();

        if ($result) {
            dd($result);
        }
        else {
            $this->error('[ * ] ' . $Telegram->error);
        }
    }
}
