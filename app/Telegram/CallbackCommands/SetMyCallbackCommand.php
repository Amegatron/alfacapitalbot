<?php
namespace App\Telegram\CallbackCommands;

use App\Core\Telegram\ReplyAgentsSupervisor;
use App\Opif;
use App\Telegram\ReplyAgents\SetMyReplyAgent;
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

        if (0 == $this->inputPif) {
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
        } elseif ($this->inputPif > 0) {
            /** @var Opif $opif */
            $opif = Opif::find($this->inputPif);

            if (!$opif) {
                $this->editMessageText([
                    "text" => "ПИФ не найден",
                ]);
                return;
            }

            $keyboard = Keyboard::make()->inline();
            /** @var SetMyCallbackCommand $command */
            $command = app(SetMyCallbackCommand::class);
            $command->setInputPif(-1);
            $button = Keyboard::inlineButton([
                'text' => "Отмена",
                'callback_data' => $command->getCallbackData(),
            ]);
            $keyboard->row($button);

            session()->put(ReplyAgentsSupervisor::FORCE_AGENT, 'setmy');
            session()->put(SetMyReplyAgent::SET_MY_PIF_ID, $this->inputPif);
            $this->replyWithMessage([
                'text' => 'Пришлите мне сообщением Ваше кол-во паев в выбранном ПИФе ("' . $opif->name . '"):',
                'reply_markup' => $keyboard,
            ]);
        } elseif ($this->inputPif == -1) {
            session()->forget([
                ReplyAgentsSupervisor::FORCE_AGENT,
                SetMyReplyAgent::SET_MY_PIF_ID,
            ]);
            $this->editMessageText([
                'text' => 'Операция отменена',
            ]);
        }
    }
}
