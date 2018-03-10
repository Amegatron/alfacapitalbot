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

        $buttonList = Keyboard::button([
            ['text' => 'Список'],
        ]);
        $buttonMy = Keyboard::button([
            ['text' => 'Мои ПИФы'],
        ]);

        $keyboard->row([$buttonList, $buttonMy]);

        $this->replyWithMessage([
            'text' => 'Приветствую!',
            'reply_markup' => $keyboard,
        ]);
    }
}