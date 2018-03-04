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
        // $userId = $this->getUpdate()->getMessage()->getFrom()->getId();
        $userId = 120482670;

        /** @var UserPifAmount[] $amounts */
        $amounts = UserPifAmount::with('opif')->where('user_id', '=', $userId)->get();

        $message = '';
        if (count($amounts) == 0) {
            $message = 'У Вас не задан не один ПИФ. Используйте команду /setmy для установки значений';
        } else {
            $total = 0;
            foreach ($amounts as $amount) {
                /** @var Opif $opif */
                $opif = $amount->opif;

                /** @var OpifCourse $latestCourse */
                $latestCourse = $opif->latestCourse();

                $currentAmount = $amount->amount * $latestCourse->course;
                $total += $currentAmount;
                $message .= $opif->name . ": " . round($currentAmount, 2) . " руб." . PHP_EOL;
            }
            $message .= PHP_EOL . "Итого: " . round($total, 2) . " руб.";
        }

        $this->info($message);
    }
}
