<?php
namespace App\Telegram\Commands;

use App\Opif;
use App\OpifCourse;
use App\UserPifAmount;
use Telegram\Bot\Commands\Command;

class MyCommand extends Command
{
    protected $name = 'my';

    protected $description = "Отображает информацию по всем Вашим ПИФам";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $userId = $this->getUpdate()->getMessage()->getFrom()->getId();

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
                $latestCourse = $opif->latestCourse;

                $currentAmount = $amount->amount * $latestCourse->course;
                $total += $currentAmount;
                $message .= $opif->name . ": " . round($currentAmount, 2) . " руб." . PHP_EOL;
            }
            $message .= PHP_EOL . "Итого: " . round($total, 2) . " руб.";
        }

        $this->replyWithMessage(['text' => $message]);
    }
}