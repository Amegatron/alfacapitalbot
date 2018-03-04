<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Telegram\Bot\Api;

class Telegram extends Controller
{
    public function onUpdate(Api $telegram)
    {
        $telegram->commandsHandler(true);
    }
}
