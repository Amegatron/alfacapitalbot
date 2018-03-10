<?php
namespace App\Telegram\ReplyAgents;

use Telegram\Bot\Objects\Update;

class DumbReplyAgent extends AbstractReplyAgent
{
    protected $name = 'dumb';

    public function handle()
    {
        $this->replyWithMessage([
            "text" => "Я робот и не в состоянии понять смысл Вашего сообщения." . PHP_EOL .
                "Пожалуйста, используйте меню для взаимодействия со мной :)",
        ]);
        return false;
    }
}