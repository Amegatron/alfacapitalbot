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
        //$this->triggerCommand('help');
        $keyboard = Keyboard::make();

        $keyboard->row(['Список', 'Мои ПИФы']);

        $this->replyWithMessage([
            'text' => 'Приветствую!',
            'reply_markup' => $keyboard,
        ]);
    }
}