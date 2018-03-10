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
            'text' => 'Text1',
        ]);
        $button2 = Keyboard::button([
            'text' => 'Text2',
        ]);

        $keyboard->row([$button1, $button2]);

        $this->replyWithMessage([
            'text' => 'Hello!',
            'reply_markup' => $keyboard,
        ]);
        $this->triggerCommand('help');
    }
}