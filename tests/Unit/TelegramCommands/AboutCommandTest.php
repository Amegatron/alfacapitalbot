<?php
namespace Tests\Unit\TelegramCommands;

use App\Telegram\Commands\AboutCommand;
use Tests\TestCase;

class AboutCommandTest extends TestCase
{
    /** @test */
    public function a_command_should_reply_with_text()
    {
        $commandMock = \Mockery::mock(AboutCommand::class)->makePartial();
        $commandMock->shouldReceive("replyWithMessage")->once();

        $commandMock->handle("test");
    }
}