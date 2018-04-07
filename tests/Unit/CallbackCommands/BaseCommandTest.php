<?php
namespace Tests\Unit\CallbackCommands;

use App\Telegram\CallbackCommands\CallbackCommand;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Chat;
use Telegram\Bot\Objects\Message;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class BaseCommandTest extends TestCase
{
    /** @test */
    public function a_command_should_return_telegram_object()
    {
        $telegramMock = \Mockery::mock(Api::class);

        $command = \Mockery::mock(CallbackCommand::class, [$telegramMock])->makePartial();

        $this->assertEquals($telegramMock, $command->getTelegram());
    }

    /** @test */
    public function a_command_should_return_correct_callback_data()
    {
        $telegramMock = \Mockery::mock(Api::class);

        $name = "testcommand";
        $parameters = [100];

        $command = \Mockery::mock(CallbackCommand::class, [$telegramMock])->makePartial();
        $command->shouldReceive("getName")->andReturn($name);
        $command->shouldReceive("getParameters")->andReturn($parameters);

        $this->assertEquals($name . ":" . implode(",", $parameters), $command->getCallbackData());
    }

    /** @test */
    public function a_command_should_throw_exception_for_too_long_data()
    {
        $telegramMock = \Mockery::mock(Api::class);

        $command = \Mockery::mock(CallbackCommand::class, [$telegramMock])->makePartial();
        $command->shouldReceive("getParameters")
            ->andReturn([random_bytes(80)]);

        $this->expectException(\InvalidArgumentException::class);

        $command->getCallbackData();
    }

    /** @test */
    public function a_command_should_not_call_answer_callback()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getCallbackQuery")->andReturnNull();

        $command = \Mockery::mock(CallbackCommand::class, [$telegram])->makePartial();

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("CallbackQuery is missing in request");

        $command->setUpdate($update);
        $command->answerCallbackQuery([]);
    }

    /** @test */
    public function a_command_should_call_answer_callback()
    {
        $queryId = 1;

        $telegram = \Mockery::mock(Api::class)->makePartial();
        $telegram->shouldReceive("answerCallbackQuery")
            ->once()
            ->withArgs(function($params) use ($queryId) {
                $this->assertTrue(is_array($params));
                $this->assertArrayHasKey("callback_query_id", $params);
                $this->assertEquals($queryId, $params['callback_query_id']);
                return true;
            });

        $callbackQuery = \Mockery::mock(CallbackQuery::class)->makePartial();
        $callbackQuery->shouldReceive("getId")->andReturn($queryId);

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getCallbackQuery")->andReturn($callbackQuery);

        $command = \Mockery::mock(CallbackCommand::class, [$telegram])->makePartial();

        $command->setUpdate($update);
        $command->answerCallbackQuery([]);
    }

    /** @test */
    public function a_command_should_call_edit_message_text_1()
    {
        $inlineMessageId = 1;

        $telegram = \Mockery::mock(Api::class)->makePartial();
        $telegram->shouldReceive("editMessageText")
            ->once()
            ->withArgs(function($params) use ($inlineMessageId) {
                $this->assertTrue(is_array($params));
                $this->assertArrayHasKey("inline_message_id", $params);
                $this->assertEquals($inlineMessageId, $params["inline_message_id"]);
                return true;
            });

        $message = \Mockery::mock(Message::class)->makePartial();

        $callbackQuery = \Mockery::mock(CallbackQuery::class)->makePartial();
        $callbackQuery->shouldReceive("getMessage")->andReturn($message);
        $callbackQuery->shouldReceive("getInlineMessageId")->andReturn($inlineMessageId);

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getCallbackQuery")->andReturn($callbackQuery);

        $command = \Mockery::mock(CallbackCommand::class, [$telegram])->makePartial();
        $command->setUpdate($update);
        $command->editMessageText([]);
    }

    /** @test */
    public function a_command_should_call_edit_message_text_2()
    {
        $chatId = 1;
        $messageId = 10;

        $telegram = \Mockery::mock(Api::class)->makePartial();
        $telegram->shouldReceive("editMessageText")
            ->once()
            ->withArgs(function($params) use ($chatId, $messageId) {
                $this->assertTrue(is_array($params));
                $this->assertArrayHasKey("chat_id", $params);
                $this->assertArrayHasKey("message_id", $params);
                $this->assertEquals($chatId, $params["chat_id"]);
                $this->assertEquals($messageId, $params["message_id"]);
                return true;
            });

        $chat = \Mockery::mock(Chat::class)->makePartial();
        $chat->shouldReceive("getId")->andReturn($chatId);

        $message = \Mockery::mock(Message::class)->makePartial();
        $message->shouldReceive("getChat")->andReturn($chat);
        $message->shouldReceive("getMessageId")->andReturn($messageId);

        $callbackQuery = \Mockery::mock(CallbackQuery::class)->makePartial();
        $callbackQuery->shouldReceive("getMessage")->andReturn($message);
        $callbackQuery->shouldReceive("getInlineMessageId")->andReturn(0);

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getCallbackQuery")->andReturn($callbackQuery);

        $command = \Mockery::mock(CallbackCommand::class, [$telegram])->makePartial();
        $command->setUpdate($update);
        $command->editMessageText([]);
    }

    /** @test */
    public function a_command_should_throw_exception()
    {
        $telegram = \Mockery::mock(Api::class)->makePartial();

        $callbackQuery = \Mockery::mock(CallbackQuery::class)->makePartial();
        $callbackQuery->shouldReceive("getMessage")->andReturnNull();

        $update = \Mockery::mock(Update::class)->makePartial();
        $update->shouldReceive("getCallbackQuery")->andReturn($callbackQuery);

        $this->expectException(\BadMethodCallException::class);
        $this->expectExceptionMessage("No callbackQuery available for editMessageText");

        $command = \Mockery::mock(CallbackCommand::class, [$telegram])->makePartial();
        $command->setUpdate($update);
        $command->editMessageText([]);
    }
}