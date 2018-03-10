<?php
namespace App\Telegram\CallbackCommands;

use App\Core\Logic\OpifLogic;

class MyInfoCallbackCommand extends CallbackCommand
{
    protected $name = 'myinfo';

    protected $userId;

    public function getParameters()
    {
        return [$this->userId];
    }

    public function setParameters($params)
    {
        $this->userId = $params[0];
    }

    public function handle()
    {
        /** @var OpifLogic $logic */
        $logic = app(OpifLogic::class);
        $message = $logic->getUserOpifSummary($this->userId);

        //$this->replyWithMessage(['text' => $message]);
        $this->editMessageText(['text' => $message]);
    }
}