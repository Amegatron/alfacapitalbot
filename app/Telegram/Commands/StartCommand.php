<?php
namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    protected $name = 'start';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        //$this->replyWithMessage(['text' => session()->getId()]);
        $this->triggerCommand('help');
    }
}