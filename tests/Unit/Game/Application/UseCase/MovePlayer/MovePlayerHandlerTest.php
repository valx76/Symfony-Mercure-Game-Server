<?php

namespace App\Tests\Unit\Game\Application\UseCase\MovePlayer;

use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Application\UseCase\MovePlayer\MovePlayerAsyncMessage;
use App\Game\Application\UseCase\MovePlayer\MovePlayerHandler;
use App\Game\Domain\Exception\PositionCollidingException;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Level\Level2;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\SharedContext\Domain\Exception\VectorOutOfAreaException;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class MovePlayerHandlerTest extends TestCase
{
    private Player $player;
    private World $world;
    private PendingLevelMessageRepositoryInterface&MockObject $pendingLevelMessageRepository;
    private NotificationGeneratorInterface&MockObject $notificationGenerator;

    public function testThrowPositionCollidingException(): void
    {
        $handler = $this->createHandler();

        $this->expectException(PositionCollidingException::class);
        $handler->__invoke(new MovePlayerAsyncMessage($this->player->id, 0, 0));
    }

    public function testThrowVectorOutOfAreaException(): void
    {
        $handler = $this->createHandler();

        $this->expectException(VectorOutOfAreaException::class);
        $handler->__invoke(new MovePlayerAsyncMessage($this->player->id, 100, 100));
    }

    public function testMove(): void
    {
        $handler = $this->createHandler();

        $this->pendingLevelMessageRepository->expects($this->once())->method('push')
            ->with($this->world, Level1::class);

        $handler->__invoke(new MovePlayerAsyncMessage($this->player->id, 3, 3));
        $this->assertSame(Level1::class, $this->player->levelName);
        $this->assertSame(3, $this->player->position->x);
        $this->assertSame(3, $this->player->position->y);
    }

    public function testTeleport(): void
    {
        $handler = $this->createHandler();

        $invokedCount = $this->exactly(2);
        $this->pendingLevelMessageRepository->expects($invokedCount)->method('push')
            ->willReturnCallback(function ($testWorld, $testLevelName) use ($invokedCount) {
                if (1 === $invokedCount->numberOfInvocations()) {
                    $this->assertSame($this->world, $testWorld);
                    $this->assertSame(Level2::class, $testLevelName);
                }

                if (2 === $invokedCount->numberOfInvocations()) {
                    $this->assertSame($this->world, $testWorld);
                    $this->assertSame(Level1::class, $testLevelName);
                }
            });

        $this->notificationGenerator->expects($this->once())->method('generateLevelChangeData')->with($this->player->id, Level2::class);

        $handler->__invoke(new MovePlayerAsyncMessage($this->player->id, 2, 3));
        $this->assertSame(Level2::class, $this->player->levelName);
        $this->assertSame(5, $this->player->position->x);
        $this->assertSame(2, $this->player->position->y);
    }

    private function createHandler(): MovePlayerHandler
    {
        $playerRepository = $this->createMock(PlayerRepositoryInterface::class);
        $worldRepository = $this->createMock(WorldRepositoryInterface::class);
        $pendingLevelMessageRepository = $this->createMock(PendingLevelMessageRepositoryInterface::class);
        $clock = $this->createMock(ClockInterface::class);
        $notificationGenerator = $this->createMock(NotificationGeneratorInterface::class);
        $levelFactory = new LevelFactory();

        $levelName = Level1::class;
        $player = new Player('playerId', 'playerName', new Vector(0, 0), new \DateTimeImmutable(), 'worldId', $levelName);
        $world = new World('worldId', 'worldName', [$player]);

        $playerRepository->method('find')->willReturn($player);
        $worldRepository->method('find')->willReturn($world);

        $this->player = $player;
        $this->world = $world;
        $this->pendingLevelMessageRepository = $pendingLevelMessageRepository;
        $this->notificationGenerator = $notificationGenerator;

        return new MovePlayerHandler($playerRepository, $worldRepository, $pendingLevelMessageRepository, $levelFactory, $clock, $notificationGenerator);
    }
}
