<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Foundation\Application;
use Telegram\Bot\Api;

class CheckForMaintenanceModeTelegram
{
    /**
     * The application implementation.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /** @var Api */
    protected $telegram;

    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Foundation\Application $app
     *
     * @param Api $telegram
     */
    public function __construct(Application $app, Api $telegram)
    {
        $this->app = $app;
        $this->telegram = $telegram;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     */
    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            $update = $this->telegram->getWebhookUpdate();
            $this->telegram->sendMessage([
                'text' => 'Бот временно недоступен (обновление сервера). Попробуйте позже.',
                'chat_id' => $update->getChat()->getId(),
            ]);

            return;
        }

        return $next($request);
    }
}