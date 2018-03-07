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
                // TODO: Пока захардкодил, переделать на шину
                if ($callbackCommandName == 'course') {
                    $command = new CourseCallbackCommand($telegram);
                    $command->setUpdate($update)
                        ->setParameters($params)
                        ->handle();
                }
            }
        }
    }
}
