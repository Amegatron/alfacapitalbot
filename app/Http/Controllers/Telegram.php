<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class Telegram extends Controller
{
    public function onUpdate(Api $telegram)
    {
        $update = $telegram->commandsHandler(true);
        $callbackQuery = $update->getCallbackQuery();
        $data = $callbackQuery->getData();
        $telegram->answerCallbackQuery([
            'callback_query_id' => $callbackQuery->getId(),
            'text' => $data,
        ]);
    }
}
