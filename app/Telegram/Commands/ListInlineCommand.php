<?php
namespace App\Telegram\Commands;

use App\Opif;
use App\Telegram\CallbackCommands\CourseCallbackCommand;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class ListInlineCommand extends Command
{
    protected $name = 'list';

    protected $description = "Список доступных ПИФов";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        /** @var Opif $pifs */
        $pifs = Opif::orderBy('id', 'asc')->get();

        $keyboard = Keyboard::make()->inline();
        foreach ($pifs as $pif) {
            $command = app()->make(CourseCallbackCommand::class, [$this->getTelegram()]);
            $command->setPifId($pif->id);
            $button = Keyboard::inlineButton([
                'text' => $pif->fullName,
                'callback_data' => $command->getCallbackData(),
            ]);
            $keyboard->row($button);
        }


        $this->replyWithMessage([
            'text' => 'Выберите ПИФ:',
            'reply_markup' => $keyboard,
        ]);
    }
}
