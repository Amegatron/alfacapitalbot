<?php

namespace App\Http\Controllers;

use App\Core\Telegram\CallbackCommandBus;
use App\Core\Telegram\ReplyAgentsSupervisor;
use Illuminate\Support\Facades\Log;
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

        if ($update->getMessage() && $text = $update->getMessage()->getText()) {
            if (strlen($text) > 0 && substr($text, 0, 1) != '/') {
                /** @var ReplyAgentsSupervisor $supervisor */
                $supervisor = app(ReplyAgentsSupervisor::class);
                $supervisor->handle($update);
            }
        }

        Log::debug("force_agent == " . session(ReplyAgentsSupervisor::FORCE_AGENT));
    }
}
