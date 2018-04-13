<?php
namespace App\Telegram\ReplyAgents;

use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class AbstractReplyAgent
{
    use Answerable;

    protected $name;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setUpdate(Update $update)
    {
        $this->update = $update;
    }

    abstract public function handle();
}
