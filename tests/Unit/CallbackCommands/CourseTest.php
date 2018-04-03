<?php
namespace Tests\Unit\CallbackCommands;

use App\Telegram\CallbackCommands\CourseCallbackCommand;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\CallbackQuery;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;
use Tests\TestCase;

class CourseTest extends TestCase
{
    /** @test */
    public function a_command_should_call_answer_callback()
    {
        $telegramMock = \Mockery::mock(Api::class);

        $userMock = \Mockery::mock(User::class)->makePartial()
            ->shouldReceive("getId")
            ->andReturn(10000)
            ->getMock();

        $callbackQueryMock = \Mockery::mock(CallbackQuery::class)->makePartial()
            ->shouldReceive("getFrom")
            ->andReturn($userMock)
            ->getMock();

        $updateMock = \Mockery::mock(Update::class)->makePartial()
            ->shouldReceive("getCallbackQuery")
            ->andReturn($callbackQueryMock)
            ->getMock();

        $commandMock = \Mockery::mock(CourseCallbackCommand::class, [$telegramMock])->makePartial();
        $commandMock->shouldReceive("answerCallbackQuery");
        $commandMock->shouldReceive("editMessageText");

        $commandMock->setUpdate($updateMock);
        $commandMock->setParameters([1]);
        $commandMock->handle();
    }
}