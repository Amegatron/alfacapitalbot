<?php
namespace App\Telegram\ReplyAgents;

use App\Core\Logic\OpifLogic;
use App\Core\Telegram\ReplyAgentsSupervisor;

class SetMyReplyAgent extends AbstractReplyAgent
{
    protected $name = 'setmy';

    const SET_MY_PIF_ID = 'set_my_pif_id';

    public function handle()
    {
        $pifId = session(self::SET_MY_PIF_ID);
        if (!$pifId) {
            return false;
        }

        $text = $this->update->getMessage()->getText();
        $userId = $this->update->getMessage()->getFrom()->getId();

        if (!is_numeric($text)) {
            $this->replyWithMessage(['text' => "Неверный формат числа. Повторите попытку."]);
            return false;
        }

        $amount = (double)$text;

        try {
            $message = (new OpifLogic)->setUserAmount($userId, $pifId, $amount);
            $this->replyWithMessage(['text' => $message]);
            session()->forget([
                self::SET_MY_PIF_ID,
                ReplyAgentsSupervisor::FORCE_AGENT
            ]);
        } catch (\Throwable $e) {
            $this->replyWithMessage(['text' => 'Ошибка']);
        }
    }
}