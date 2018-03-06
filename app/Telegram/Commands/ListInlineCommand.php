<?php
namespace App\Telegram\Commands;

use App\Opif;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class ListInlineCommand extends Command
{
    protected $name = 'list2';

    /**
     * {@inheritdoc}
     */
    public function handle($arguments)
    {
        /** @var Opif $pifs */
        $pifs = Opif::orderBy('id', 'asc')->get();

        $keyboard = Keyboard::make()->inline();
        foreach ($pifs as $pif) {
            $button = Keyboard::inlineButton([
                'text' => $pif->fullName,
                'callback_data' => 'pif_' . $pif->id,
            ]);
            $keyboard->row($button);
        }


        $this->replyWithMessage([
            'text' => 'Выберите ПИФ:',
            'reply_markup' => $keyboard,
        ]);
    }
}