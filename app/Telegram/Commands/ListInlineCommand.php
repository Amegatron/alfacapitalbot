<?php
namespace App\Telegram\Commands;

use App\Opif;
use App\Telegram\CallbackCommands\CourseCallbackCommand;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class ListInlineCommand extends Command
{
    protected $name = 'list2';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        /** @var Opif $pifs */
        $pifs = Opif::orderBy('id', 'asc')->get();

        $keyboard = Keyboard::make()->inline();
        foreach ($pifs as $pif) {
            $command = new CourseCallbackCommand($this->getTelegram());
            $command->setPifId($pif->id);
            $callbackData = $command->getCallbackData();
            Log::info($callbackData);
            $button = Keyboard::inlineButton([
                'text' => $pif->fullName,
                'callback_data' => $callbackData,
            ]);
            $keyboard->row($button);
        }


        $this->replyWithMessage([
            'text' => 'Выберите ПИФ:',
            'reply_markup' => $keyboard,
        ]);
    }
}