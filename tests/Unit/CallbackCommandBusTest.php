<?php
namespace Tests\Unit;

use App\Core\Telegram\CallbackCommandBus;
use App\Telegram\CallbackCommands\CallbackCommand;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Tests\TestCase;

class CallbackCommandBusTest extends TestCase
{
    public function tearDown()
    {
        parent::tearDown(); // TODO: Change the autogenerated stub
        \Mockery::close();
    }

    /** @test */
    public function a_bus_should_call_the_command()
    {
        $telegramMock = \Mockery::mock(Api::class);
        $updateMock = \Mockery::mock(Update::class);

        $commandMock = \Mockery::mock(CallbackCommand::class, [$telegramMock])->makePartial();
        $commandMock->shouldReceive("getName")->andReturn("testcommand");
        $commandMock->shouldReceive("setParameters")
            ->with(["test"])
            ->andReturnSelf();
        $commandMock->shouldReceive("handle");

        $this->app->bind(get_class($commandMock), function ($app) use ($commandMock) {
            return $commandMock;
        });

        $bus = new CallbackCommandBus($telegramMock);
        $bus->addCommand($commandMock);
        $bus->handle("testcommand:test", $updateMock);
    }

    /** @test */
    public function a_bus_should_not_call_the_command()
    {
        $telegramMock = \Mockery::mock(Api::class);
        $updateMock = \Mockery::mock(Update::class);

        $commandMock = \Mockery::mock(CallbackCommand::class, [$telegramMock])->makePartial();
        $commandName = "testcommand";
        $commandMock->shouldReceive("getName")->andReturn($commandName);
        $commandMock->shouldReceive("setParameters")
            ->never()
            ->andReturnSelf();
        $commandMock->shouldReceive("handle")->never();

        $this->app->bind(get_class($commandMock), function ($app) use ($commandMock) {
            return $commandMock;
        });

        $bus = new CallbackCommandBus($telegramMock);
        $bus->addCommand($commandMock);
        $bus->removeCommands([$commandName]);
        $bus->handle("testcommand:test", $updateMock);
    }
}