<?php
namespace App\Telegram\Commands;

use App\Opif;
use App\OpifCourse;
use App\UserPifAmount;
use Telegram\Bot\Commands\Command;

class CourseCommand extends Command
{
    protected $name = 'course';

    protected $description = 'Отображает стоимость одного пая выбранного ПИФа (/course <# ПИФа>)';
    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        if (!is_numeric($arguments)) {
            $this->replyWithMessage(['text' => $this->description]);
            return;
        }

        $pifId = (int)$arguments;
        /** @var Opif $pif */
        $pif = Opif::find($pifId);

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
            $text .= "Объем ваших средств в этом ПИФ составляет: " . round($userPifAmount->amount * $course->course, 2);
        }

        $this->replyWithMessage(['text' => $text]);
    }
}