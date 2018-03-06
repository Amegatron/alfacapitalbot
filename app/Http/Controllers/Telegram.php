<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class Telegram extends Controller
{
    public function onUpdate(Api $telegram)
    {
        $update = $telegram->commandsHandler(true);
        $callbackQuery = $update->getCallbackQuery();
        if ($callbackQuery) {
            $data = $callbackQuery->getData();
            $telegram->answerCallbackQuery([
                'callback_query_id' => $callbackQuery->getId(),
            ]);
            $telegram->editMessageText([
                'text' => session()->getId() . " :: " . $data,
                'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
                'message_id' => $callbackQuery->getMessage()->getMessageId(),
            ]);
        }
    }
}
