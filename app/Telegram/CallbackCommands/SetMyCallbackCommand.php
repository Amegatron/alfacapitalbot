<?php
namespace App\Telegram\CallbackCommands;

use App\Opif;
use Telegram\Bot\Keyboard\Keyboard;

class SetMyCallbackCommand extends CallbackCommand
{
    protected $name = 'setmy';

    protected $inputPif;

    public function getParameters()
    {
        return [$this->inputPif];
    }

    public function setParameters($params)
    {
        $this->inputPif = $params[0];
    }

    public function setInputPif($pif = 0)
    {
        $this->inputPif = $pif;
    }

    public function handle()
    {
        $this->answerCallbackQuery();

        if (!$this->inputPif) {
            /** @var Opif $pifs */
            $pifs = Opif::orderBy('id', 'asc')->get();

            $keyboard = Keyboard::make()->inline();
            foreach ($pifs as $pif) {
                /** @var SetMyCallbackCommand $command */
                $command = app(SetMyCallbackCommand::class);
                $command->setInputPif($pif->id);

                $button = Keyboard::inlineButton([
                    'text' => $pif->fullName,
                    'callback_data' => $command->getCallbackData(),
                ]);
                $keyboard->row($button);
            }

            $this->editMessageText([
                'text' => 'Выберите ПИФ:',
                'reply_markup' => $keyboard,
            ]);
        } else {
            /** @var Opif $opif */
            $opif = Opif::find($this->inputPif);
            $this->replyWithMessage([
                'text' => 'Пришлите мне сообщением Ваше кол-во паев в выбранном ПИФе ("' . $opif->name . '""):',
            ]);
        }
    }
}