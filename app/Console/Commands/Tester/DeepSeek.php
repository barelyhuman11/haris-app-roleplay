<?php

namespace App\Console\Commands\Tester;

use Illuminate\Console\Command;
use App\Helpers\API\DeepSeek as DeepSeekAPI;
use Illuminate\Support\Facades\Storage;

class DeepSeek extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:deepseek';

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
        $prompt = $this->ask('Prompt');
        $DeepSeek = new DeepSeekAPI();
        $result = $DeepSeek->addMessage('You are a helpful assistant.')
            ->addMessage($prompt, 'user')
            ->result();
        if ($result) {
            // Debug purpose
            Storage::disk('local')->put('deepseek.txt', json_encode($result));
            dd($result);
        }
        else {
            $this->error('[ * ] ' . $DeepSeek->error);
        }
    }
}
