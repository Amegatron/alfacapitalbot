<?php
namespace App\Telegram\Commands;

use Telegram\Bot\Commands\Command;

class TestSessionCommand extends Command
{
    protected $name = 'session';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $lastCommands = session()->get('last', []);
        if (count($lastCommands) > 1) {
            array_shift($lastCommands);
        }
        $lastCommands[] = $arguments;
        session('last', $lastCommands);

        $message = implode("\n", $lastCommands);
        $this->replyWithMessage(['text' => $message]);
    }
}