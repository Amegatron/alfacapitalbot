<?php
namespace App\Telegram\CallbackCommands;

use App\Opif;
use App\OpifCourse;
use App\UserPifAmount;

class CourseCallbackCommand extends CallbackCommand
{

    protected $name = 'course';

    protected $pifId;

    public function getPifId()
    {
        return $this->pifId;
    }

    public function setPifId($pifId)
    {
        $this->pifId = $pifId;

        return $this;
    }

    public function getParameters()
    {
        return [
            'pif_id' => $this->pifId,
        ];
    }

    public function setParameters(...$params)
    {
        $this->pifId = $params[0];
    }

    public function handle()
    {
        /** @var Opif $pif */
        $pif = Opif::find($this->pifId);

        if (!$pif) {
            $this->replyWithMessage(['text' => "ПИФ с таким номером не найден"]);
            return;
        }

        /** @var OpifCourse $course */
        $course = $pif->latestCourse();

        if (!$course) {
            $this->replyWithMessage(['text' => 'Нет данных']);
            return;
        }

        $userId = $this->update->getMessage()->getFrom()->getId();

        $userPifAmount = UserPifAmount::where('user_id', '=', $userId)
            ->where('opif_id', '=', $pif->id)
            ->first();

        $text = "Стоимость пая ПИФа \"{$pif->name}\" на {$course->date}: " . $course->course . " руб.";

        if ($userPifAmount) {
            $text .= PHP_EOL;
            $text .= "Объем ваших средств в этом ПИФ составляет: " . round($userPifAmount->amount * $course->course, 2) . " руб.";
        }

        $this->replyWithMessage(['text' => $text]);
    }
}