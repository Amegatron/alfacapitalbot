<?php
namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class StartTelegramSession extends StartSession
{
    /**
     * Get the session implementation from the manager.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Session\Session
     */
    public function getSession(Request $request)
    {
        /** @var Api $telegram */
        $telegram = app('telegram');
        $update = $telegram->getWebhookUpdates();
        $sessionName = null;
        if ($update) {
            $sessionName = $update->getMessage()->getFrom()->getId();
        }

        return tap($this->manager->driver(), function ($session) use ($sessionName) {
            $session->setId(str_pad($sessionName, 40, "0", STR_PAD_LEFT));
        });
    }
}