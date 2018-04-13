<?php
namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class AboutCommand extends Command
{
    protected $name = 'about';

    protected $description = 'Информация о боте';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $message = <<<TEXT
Данный бот позволяет отслеживать стоимости паев в УК Альфа-Капитал.

Бот написан на PHP с использованием открытой библиотеки telegram-bot-sdk от irazasyed (Syed Irfaq R.).

Пожелания приветствуются.

Автор: Александр Егоров (amego2006@gmail.com).
TEXT;

        $this->replyWithMessage([
            'text' => $message,
        ]);
    }
}
