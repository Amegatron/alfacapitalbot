<?php

namespace App\Http\Controllers;

use App\Telegram\CallbackCommands\CourseCallbackCommand;
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
            if (preg_match('~^([a-z]+):(.*)~is', $data, $matches)) {
                $callbackCommandName = $matches[1];
                $paramsRaw = $matches[2];
                $params = explode(",", $paramsRaw);
                if ($callbackCommandName == 'course') {
                    $command = new CourseCallbackCommand($telegram);
                    $command->setUpdate($update)
                        ->setParameters($params)
                        ->handle();
                }
            }


            // $telegram->answerCallbackQuery([
            //     'callback_query_id' => $callbackQuery->getId(),
            // ]);

            // $telegram->editMessageText([
            //     'text' => session()->getId() . " :: " . $data,
            //     'chat_id' => $callbackQuery->getMessage()->getChat()->getId(),
            //     'message_id' => $callbackQuery->getMessage()->getMessageId(),
            // ]);
        }
    }
}
