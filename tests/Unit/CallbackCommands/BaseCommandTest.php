<?php
namespace Tests\Unit\CallbackCommands;

use App\Telegram\CallbackCommands\CallbackCommand;
use Telegram\Bot\Api;
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
}