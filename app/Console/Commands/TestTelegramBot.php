<?php

namespace App\Console\Commands;

use App\UserPifAmount;
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
        /** @var UserPifAmount[] $amounts */
        $amounts = UserPifAmount::with('opif')->where('user_id', '=', 120482670)->get();

        foreach ($amounts as $amount) {
            $this->info($amount->opif->name);
            $this->info($amount->amount);
        }
    }
}
