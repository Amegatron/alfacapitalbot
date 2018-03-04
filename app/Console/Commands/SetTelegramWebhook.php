<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Telegram\Bot\Api;

class SetTelegramWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook {action}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sets or disables webhook for Telegram';

    /** @var Api */
    protected $telegram;

    /**
     * Create a new command instance.
     *
     * @param Api $telegram
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
        $action = strtolower($this->input->getArgument('action'));
        switch ($action) {
            case 'set':
                $host = env('APP_URL');
                $token = env('TELEGRAM_WEBHOOK_TOKEN');
                $url = route('telegram_webhook');
                $this->telegram->setWebhook([
                    'url' => $url,
                    'max_connections' => 20,
                ]);
                $this->info('WebHook was successfully set.');
                break;
            case 'unset':
                $this->telegram->removeWebhook();
                $this->info('WebHook was successfully removed.');
                break;
        }
    }
}
