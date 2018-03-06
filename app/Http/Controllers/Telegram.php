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
            // $telegram->answerCallbackQuery([
            //     'callback_query_id' => $callbackQuery->getId(),
            //     'text' => $data,
            // ]);
            $telegram->answerCallbackQuery([
                'callback_query_id' => $callbackQuery->getId(),
            ]);
            Log::info($update->getRawResponse());
            Log::info($callbackQuery->getInlineMessageId());
            // $telegram->editMessageText([
            //     'text' => $data,
            //     'inline_message_id' => $callbackQuery->getInlineMessageId(),
            //     // 'message_id' => $callbackQuery->getMessage()->getMessageId(),
            // ]);
            $telegram->editMessageText([
                'text' => $data,
                'chat_id' => $update->getMessage()->getChat()->getId(),
                'message_id' => $update->getMessage()->getMessageId(),
            ]);
            // $telegram->sendMessage([
            //     'text' => $data,
            //     'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
            // ]);
        }
    }
}
