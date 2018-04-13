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
        $keyboard = Keyboard::make();
        $button1 = Keyboard::button([
            'text' => 'Список 📃',
        ]);
        $button2 = Keyboard::button([
            'text' => 'Мои ПИФы ⚙️',
        ]);
        $keyboard->row($button1, $button2);

        $this->replyWithMessage([
            'text' => 'Приветствую!',
            'reply_markup' => $keyboard,
        ]);
        //$this->triggerCommand('help');
    }
}
