<?php
namespace App\Telegram\ReplyAgents;

class MenuReplyAgent extends AbstractReplyAgent
{
    protected $name = 'menu';

    public function handle()
    {
        $message = $this->getUpdate()->getMessage()->getText();

        switch ($message) {
            case 'Список':
                $this->getTelegram()->triggerCommand('list', $this->getUpdate());
                return false;
            case 'Мои ПИФы':
                $this->getTelegram()->triggerCommand('my', $this->getUpdate());
                return false;
        }

        return true;
    }
}