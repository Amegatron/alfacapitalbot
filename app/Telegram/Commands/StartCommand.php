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

        $btn1 = Keyboard::button('Список');
        $btn2 = Keyboard::button('Мои ПИФы');
        $keyboard->row([$btn1, $btn2]);

        $this->replyWithMessage([
            'text' => 'Приветствую!',
            'reply_markup' => $keyboard,
        ]);
    }
}