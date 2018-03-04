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
    protected $signature = 'telegram:test {--messages=}';

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
        $limit = $this->input->getOption('messages');
        if (!is_numeric($limit)) {
            throw new \Exception("--messages option must be an integer");
        }
        $limit = (int)$limit;

        $counter = 0;
        while ($counter < $limit) {
            try {
                $updates = $this->telegram->commandsHandler();
                if (!empty($updates)) {
                    $counter++;
                }
            } catch (\Throwable $e) {
                $this->error($e->getMessage());
            }
            sleep(2);
        }
    }
}
