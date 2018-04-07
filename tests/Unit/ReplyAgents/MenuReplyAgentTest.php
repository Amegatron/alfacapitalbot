<?php
namespace Tests\Unit\ReplyAgents;

use App\Telegram\ReplyAgents\MenuReplyAgent;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class MenuReplyAgentTest extends TestCase
{
    /** @test */
    public function an_agent_should_trigger_my_command()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $message = \Mockery::mock(Message::class);
        $message->shouldReceive("getText")->andReturn("Мои ПИФы");

        $update = \Mockery::mock(Update::class);
        $update->shouldReceive("getMessage")->andReturn($message);

        $telegram->shouldReceive("triggerCommand")
            ->with("my", $update);

        $agent = new MenuReplyAgent($telegram);
        $agent->setUpdate($update);

        $result = $agent->handle();
        $this->assertFalse($result);
    }

    /** @test */
    public function an_agent_should_trigger_list_command()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $message = \Mockery::mock(Message::class);
        $message->shouldReceive("getText")->andReturn("Список");

        $update = \Mockery::mock(Update::class);
        $update->shouldReceive("getMessage")->andReturn($message);

        $telegram->shouldReceive("triggerCommand")
            ->with("list", $update);

        $agent = new MenuReplyAgent($telegram);
        $agent->setUpdate($update);

        $result = $agent->handle();
        $this->assertFalse($result);
    }

    /** @test */
    public function an_agent_should_not_trigger_any_commands()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $message = \Mockery::mock(Message::class);
        $message->shouldReceive("getText")->andReturn(random_bytes(20));

        $update = \Mockery::mock(Update::class);
        $update->shouldReceive("getMessage")->andReturn($message);

        $telegram->shouldReceive("triggerCommand")->never();

        $agent = new MenuReplyAgent($telegram);
        $agent->setUpdate($update);

        $result = $agent->handle();
        $this->assertTrue($result);
    }
}