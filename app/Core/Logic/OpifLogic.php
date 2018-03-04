<?php
namespace App\Core\Logic;

use App\Opif;
use App\OpifCourse;
use App\UserPifAmount;

class OpifLogic
{
    public function getUserOpifSummary($userId)
    {
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

        return $message;
    }
}