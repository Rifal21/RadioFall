<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ClearChatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all live chat messages';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \App\Models\Message::truncate();
        $this->info('Live chat has been cleared successfully!');
    }
}
