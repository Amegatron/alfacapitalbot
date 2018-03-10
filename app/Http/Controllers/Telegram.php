<?php

namespace App\Http\Controllers;

use App\Core\Telegram\CallbackCommandBus;
use Telegram\Bot\Api;

class Telegram extends Controller
{
    public function onUpdate(Api $telegram)
    {
        $update = $telegram->commandsHandler(true);
        $callbackQuery = $update->getCallbackQuery();
        if ($callbackQuery) {
            /** @var CallbackCommandBus $bus */
            $bus = app(CallbackCommandBus::class);
            $data = $callbackQuery->getData();
            $bus->handle($data, $update);
        }
    }
}
