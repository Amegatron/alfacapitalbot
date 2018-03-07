<?php
namespace App\Telegram\Commands;

use App\Opif;
use Telegram\Bot\Commands\Command;

class ListCommand extends Command
{
    protected $name = 'list_old';

    protected $description = "Список доступных ПИФов";

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        $pifs = Opif::orderBy('id', 'asc')->get();

        $message = '';

        foreach ($pifs as $pif) {
            $message .= $pif->id . '. ' . $pif->fullName . PHP_EOL;
        }

        $this->replyWithMessage(['text' => $message]);
    }
}