<?php
namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class StartCommand extends Command
{
    protected $name = 'start';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $keyboard = Keyboard::make([['Text1', 'Text2']]);

        $this->replyWithMessage([
            'text' => 'Приветствую!',
            'reply_markup' => $keyboard,
        ]);
        $this->triggerCommand('help');
    }
}