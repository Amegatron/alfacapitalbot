<?php
namespace Tests\Unit\TelegramCommands;

use App\Telegram\Commands\StartCommand;
use Tests\TestCase;

class StartCommandTest extends TestCase
{
    /** @test */
    public function a_command_should_reply_with_text()
    {
        $commandMock = \Mockery::mock(StartCommand::class)->makePartial();
        $commandMock->shouldReceive("replyWithMessage")->once();

        $commandMock->handle("test");
    }
}