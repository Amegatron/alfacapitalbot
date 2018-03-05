<?php

Route::any('/' . env('TELEGRAM_WEBHOOK_TOKEN', 'some-webhook-token') .'/telegram', 'Telegram@onUpdate')
    ->name('telegram_webhook');