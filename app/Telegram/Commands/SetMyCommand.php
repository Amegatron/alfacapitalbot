<?php
namespace App\Telegram\Commands;

use App\Core\Logic\OpifLogic;
use Telegram\Bot\Commands\Command;

class SetMyCommand extends Command
{
    protected $name = 'setmy';

    protected $description = 'Устанавливает Ваше кол-во паев указанного ПИФа (/setmy <#ПИФа> <кол-во>)';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        if (!preg_match('~^(\d+) (\d+(?:\.\d+)?)$~is', $arguments, $matches)) {
            $this->replyWithMessage(['text' => $this->description]);
            return;
        }

        $pifId = (int)$matches[1];
        $amount = (double)$matches[2];
        $userId = $this->update->getMessage()->getFrom()->getId();

        try {
            $message = (new OpifLogic)->setUserAmount($userId, $pifId, $amount);
            $this->replyWithMessage(['text' => $message]);
        } catch (\Throwable $e) {
            $this->replyWithMessage(['text' => 'Ошибка']);
        }


    }
}