<?php
namespace Tests\Unit\ReplyAgents;

use App\Core\Logic\OpifLogic;
use App\Opif;
use App\Telegram\ReplyAgents\SetMyReplyAgent;
use function foo\func;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;
use Tests\TestCase;

class SetMyReplyAgentTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function an_agent_should_return_false()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $agent = new SetMyReplyAgent($telegram);
        $result = $agent->handle();

        $this->assertFalse($result);
    }

    /** @test */
    public function an_agent_should_reply_with_error_message()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $opif = Opif::create([
            'name' => 'Test OPIF',
            'fullName' => 'Test OPIF',
            'publicDataUrl' => 'http://someurl.com',
        ]);

        session()->put(SetMyReplyAgent::SET_MY_PIF_ID, $opif->id);

        $message = \Mockery::mock(Message::class)->makePartial();
        $message->shouldReceive("getText")->andReturn("asdfasdf");

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getMessage")->andReturn($message);

        $agent = \Mockery::mock(SetMyReplyAgent::class, [$telegram])->makePartial();
        $agent->shouldReceive("replyWithMessage")
            ->once()
            ->withArgs(function($arg) {
                $this->assertTrue(is_array($arg));
                $this->assertArrayHasKey("text", $arg);
                $this->assertContains("Неверный формат числа", $arg["text"]);
                return true;
            });

        $agent->setUpdate($update);
        $result = $agent->handle();
        $this->assertFalse($result);
    }

    /** @test */
    public function an_agent_should_set_amount()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $opif = Opif::create([
            'name' => 'Test OPIF',
            'fullName' => 'Test OPIF',
            'publicDataUrl' => 'http://someurl.com',
        ]);

        $userId = 100000;
        $amount = 100;

        session()->put(SetMyReplyAgent::SET_MY_PIF_ID, $opif->id);

        $user = \Mockery::mock(User::class)->makePartial();
        $user->shouldReceive("getId")->andReturn($userId);

        $message = \Mockery::mock(Message::class)->makePartial();
        $message->shouldReceive("getText")->andReturn($amount);
        $message->shouldReceive("getFrom")->andReturn($user);

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getMessage")->andReturn($message);

        $replyText = "OK SET";
        $opifLogic = \Mockery::mock(OpifLogic::class)->makePartial();
        $opifLogic->shouldReceive("setUserAmount")
            ->once()
            ->with($userId, $opif->id, $amount)
            ->andReturn($replyText);

        $this->app->bind(OpifLogic::class, function($app) use ($opifLogic) {
            return $opifLogic;
        });

        $agent = \Mockery::mock(SetMyReplyAgent::class, [$telegram])->makePartial();
        $agent->shouldReceive("replyWithMessage")
            ->once()
            ->withArgs(function($arg) use ($replyText) {
                $this->assertTrue(is_array($arg));
                $this->assertArrayHasKey("text", $arg);
                $this->assertContains($replyText, $arg["text"]);
                return true;
            });

        $agent->setUpdate($update);
        $agent->handle();
    }
}