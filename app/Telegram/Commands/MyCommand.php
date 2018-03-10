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

        /** @var OpifLogic $logic */
        $logic = app(OpifLogic::class);
        $message = $logic->getUserOpifSummary($userId);

        $this->replyWithMessage(['text' => $message]);
    }
}