<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class TestTelegramBot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests Telegram bot';

    /** @var Api */
    protected $telegram;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Api $telegram)
    {
        parent::__construct();
        $this->telegram = $telegram;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        while (true) {
            try {
                $updates = $this->telegram->commandsHandler();
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
            sleep(1);
        }
    }
}
