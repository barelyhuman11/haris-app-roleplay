<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

class Export extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export {path? : Output JSON file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export messages to a JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $messages = [];

        foreach (Message::orderBy('id')->cursor() as $msg) {
            $messages[] = [
                'role' => $msg->type === 'USER' ? 'user' : 'assistant',
                'content' => $msg->message,
            ];
        }

        $outputPath = $this->argument('path') ?? storage_path('app/messages.json');
        $directory = dirname($outputPath);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $json = json_encode($messages, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
        file_put_contents($outputPath, $json);

        $this->info("Exported " . count($messages) . " messages to {$outputPath}");

        return Command::SUCCESS;
    }
}
