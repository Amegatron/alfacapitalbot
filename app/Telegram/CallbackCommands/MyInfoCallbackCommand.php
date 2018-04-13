<?php
namespace App\Telegram\CallbackCommands;

use App\Core\Logic\OpifLogic;

class MyInfoCallbackCommand extends CallbackCommand
{
    protected $name = 'myinfo';

    protected $userId;

    public function getParameters()
    {
        return [];
    }

    public function setParameters($params)
    {
        return;
    }

    public function handle()
    {
        $userId = $this->getUpdate()->getCallbackQuery()->getFrom()->getId();

        /** @var OpifLogic $logic */
        $logic = app(OpifLogic::class);
        $message = $logic->getUserOpifSummary($userId);

        $this->answerCallbackQuery();

        $this->editMessageText(['text' => $message]);
    }
}
