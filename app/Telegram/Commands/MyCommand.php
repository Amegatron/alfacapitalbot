<?php
namespace App\Telegram\Commands;

use App\Core\Logic\OpifLogic;
use Telegram\Bot\Commands\Command;

class MyCommand extends Command
{
    protected $name = 'my';

    protected $description = "Отображает информацию по всем Вашим ПИФам";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $userId = $this->getUpdate()->getMessage()->getFrom()->getId();

        $logic = new OpifLogic();
        $message = $logic->getUserOpifSummary($userId);

        $this->replyWithMessage(['text' => $message]);
    }
}