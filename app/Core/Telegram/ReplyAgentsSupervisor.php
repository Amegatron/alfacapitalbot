<?php
namespace App\Core\Telegram;

use App\Telegram\ReplyAgents\AbstractReplyAgent;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class ReplyAgentsSupervisor
{
    const FORCE_AGENT = 'force_agent';

    /** @var AbstractReplyAgent[] */
    protected $agents = [];

    protected $telegram;

    public function __construct(Api $telegram)
    {
        $this->telegram = $telegram;

        $agents = config('telegram.replyAgents', []);
        $this->addAgents($agents);
    }

    public function addAgent($agent)
    {
        /** @var AbstractReplyAgent $agent */
        $agent = $this->resolveAgent($agent);

        $this->agents[$agent->getName()] = $agent;
    }

    public function addAgents($agents)
    {
        foreach ($agents as $agent) {
            $this->addAgent($agent);
        }
    }

    public function removeAgent($name)
    {
        unset($this->agents[$name]);
    }

    public function removeAgents($names)
    {
        foreach ($names as $name) {
            $this->removeAgent($name);
        }
    }

    protected function resolveAgent($agent)
    {
        if ($agent instanceof AbstractReplyAgent) {
            return $agent;
        } else if (is_string($agent) && class_exists($agent)) {
            $agentObj = new $agent($this->telegram);
            if ($agentObj instanceof AbstractReplyAgent) {
                return $agentObj;
            }
        }

        throw new \Exception("Could not resolve agent.");
    }

    public function handle(Update $update)
    {
        Log::debug('Session :: ' . self::FORCE_AGENT . ' == ' . session(self::FORCE_AGENT));
        if (session()->has(self::FORCE_AGENT) && isset($this->agents[$agentName = session(self::FORCE_AGENT)])) {
            $agent = $this->agents[$agentName];
            $agent->setUpdate($update);
            $agent->handle();
        } else {
            foreach ($this->agents as $agent) {
                $agent->setUpdate($update);
                if (false === $agent->handle()) {
                    break;
                }
            }
        }
    }
}