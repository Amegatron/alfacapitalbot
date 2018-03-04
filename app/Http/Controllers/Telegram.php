<?php

namespace App\Http\Controllers;

use Telegram\Bot\Api;

class Telegram extends Controller
{
    public function onUpdate(Api $telegram)
    {
        $telegram->commandsHandler(true);
    }
}
