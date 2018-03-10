<?php
namespace App\Core\Telegram;

use App\Telegram\ReplyAgents\AbstractReplyAgent;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class ReplyAgentsSupervisor
{
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
        if (session()->has('force_agent') && isset($this->agents[$agentName = session('force_agent')])) {
            $agent = $this->agents[$agentName];
            $agent->handle($update);
        } else {
            foreach ($this->agents as $agent) {
                if (false === $agent->handle($update)) {
                    break;
                }
            }
        }
    }
}