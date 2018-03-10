<?php
namespace App\Telegram\CallbackCommands;

class SetMyCallbackCommand extends CallbackCommand
{
    protected $name = 'setmy';

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
        $this->editMessageText(['text' => 'not implemented yet']);
    }
}