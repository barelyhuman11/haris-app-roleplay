<?php

namespace App\Console\Commands;

use App\Models\Message;
use Illuminate\Console\Command;

class SetBatch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'set-batch';

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
        $nextBatch = (Message::max('batch') ?? 0) + 1;

        $updated = Message::whereNull('batch')->update(['batch' => $nextBatch]);

        $this->info("Updated {$updated} message(s) to batch {$nextBatch}.");
    }
}
