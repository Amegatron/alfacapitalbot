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

        $buttons = [];
        foreach ($pifs as $pif) {
            $button = Keyboard::inlineButton([
                'text' => $pif->fullName,
                'callback_data' => 'pif_' . $pif->id,
            ]);
            $buttons[] = [$button];
        }
        $keyboard = Keyboard::make($buttons)->inline();

        $this->replyWithMessage([
            'text' => '',
            'reply_markup' => $keyboard,
        ]);
    }
}