<?php
namespace App\Telegram\ReplyAgents;

class MenuReplyAgent extends AbstractReplyAgent
{
    protected $name = 'menu';

    public function handle()
    {
        $message = $this->getUpdate()->getMessage()->getText();

        if (strpos($message, 'Список') === 0) {
            $this->getTelegram()->triggerCommand('list', $this->getUpdate());
            return false;
        } else if (strpos($message, 'Мои ПИФы') === 0) {
            $this->getTelegram()->triggerCommand('my', $this->getUpdate());
            return false;
        }

        return true;
    }
}