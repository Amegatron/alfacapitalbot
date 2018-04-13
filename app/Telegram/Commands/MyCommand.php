<?php
namespace App\Telegram\Commands;

use App\Core\Logic\OpifLogic;
use App\Telegram\CallbackCommands\MyInfoCallbackCommand;
use App\Telegram\CallbackCommands\SetMyCallbackCommand;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class MyCommand extends Command
{
    protected $name = 'my';

    protected $description = "Отображает информацию по всем Вашим ПИФам";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $keyboard = Keyboard::make()->inline();

        /** @var MyInfoCallbackCommand $command */
        $command = app(MyInfoCallbackCommand::class);
        $button = Keyboard::inlineButton([
            'text' => 'Информация',
            'callback_data' => $command->getCallbackData(),
        ]);
        $keyboard->row($button);

        /** @var SetMyCallbackCommand $command */
        $command = app(SetMyCallbackCommand::class);
        $command->setInputPif(0);
        $button = Keyboard::inlineButton([
            'text' => 'Установить',
            'callback_data' => $command->getCallbackData(),
        ]);
        $keyboard->row($button);

        $this->replyWithMessage([
            'text' => 'Ваши ПИФы:',
            'reply_markup' => $keyboard,
        ]);
    }
}
