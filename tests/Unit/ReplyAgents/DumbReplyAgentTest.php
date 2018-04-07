<?php
namespace Tests\Unit\ReplyAgents;

use App\Telegram\ReplyAgents\DumbReplyAgent;
use Telegram\Bot\Api;
use Tests\TestCase;

class DumbReplyAgentTest extends TestCase
{
    /** @test */
    public function an_agent_should_reply_with_dumb_text()
    {
        $telegramMock = \Mockery::mock(Api::class)->makePartial();

        $agent = \Mockery::mock(DumbReplyAgent::class, [$telegramMock])->makePartial();
        $agent->shouldReceive("replyWithMessage")
            ->withArgs(function($arg) {
                $this->assertTrue(is_array($arg));
                $this->assertArrayHasKey("text", $arg);
                return true;
            })
            ->once();

        $result = $agent->handle();
        $this->assertFalse($result);
    }
}