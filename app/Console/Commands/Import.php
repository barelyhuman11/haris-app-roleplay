<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

class Import extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import {path? : Input JSON file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import messages from a JSON file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $inputPath = $this->argument('path') ?? storage_path('app/messages.json');

        if (!is_file($inputPath)) {
            $this->error("File not found: {$inputPath}");

            return Command::FAILURE;
        }

        $messages = json_decode(file_get_contents($inputPath), true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($messages)) {
            $this->error("Invalid JSON format in {$inputPath}");

            return Command::FAILURE;
        }

        $count = 0;

        foreach ($messages as $item) {
            if (!is_array($item) || !isset($item['role'], $item['content'])) {
                continue;
            }

            if ($item['role'] === 'system') {
                continue;
            }

            Message::create([
                'type' => $item['role'] === 'user' ? 'USER' : 'AI',
                'message' => $item['content'],
            ]);

            $count++;
        }

        $this->info("Imported {$count} messages from {$inputPath}");

        return Command::SUCCESS;
    }
}
