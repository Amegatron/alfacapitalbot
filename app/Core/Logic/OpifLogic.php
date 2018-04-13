<?php
namespace App\Core\Logic;

use App\Opif;
use App\OpifCourse;
use App\UserPifAmount;

class OpifLogic
{
    public function setUserAmount($userId, $opif, $amount)
    {
        if (is_numeric($opif)) {
            $opif = Opif::find($opif);
        }

        if (is_null($opif)) {
            throw new \Exception("OPIF not found");
        }

        $existing = UserPifAmount::where('user_id', '=', $userId)
            ->where('opif_id', '=', $opif->id)
            ->first();

        $action = null;
        if ($existing) {
            if ($amount > 0) {
                $existing->amount = $amount;
                $existing->save();
                $action = 'update';
            } else {
                $existing->delete();
                $action = 'remove';
            }
        } else {
            if ($amount > 0) {
                $existing = UserPifAmount::create([
                    'user_id' => $userId,
                    'opif_id' => $opif->id,
                    'amount' => $amount,
                ]);
                $action = 'insert';
            } else {
                $action = 'remove';
            }
        }

        $message = "Кол-во паев для ПИФа \"{$opif->fullName}\" ";
        if ($action == 'update') {
            $message .= "обновлено до значения ";
        } elseif ($action == 'insert') {
            $message .= "установлено в ";
        }
        $message .= $amount;

        if ($action == 'remove') {
            $message = 'Данные о кол-ве паев были удалены';
        }

        return $message;
    }

    public function getUserOpifSummary($userId)
    {
        /** @var UserPifAmount[] $amounts */
        $amounts = UserPifAmount::with('opif')->where('user_id', '=', $userId)->get();

        $message = '';
        if (count($amounts) == 0) {
            $message = 'У Вас не задан ни один ПИФ. Используйте команду /my для установки значений';
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
