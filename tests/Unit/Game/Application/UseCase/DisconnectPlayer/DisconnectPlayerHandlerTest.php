<?php

namespace App\Tests\Unit\Game\Application\UseCase\DisconnectPlayer;

use App\Game\Application\UseCase\DisconnectPlayer\DisconnectPlayerAsyncMessage;
use App\Game\Application\UseCase\DisconnectPlayer\DisconnectPlayerHandler;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;

class DisconnectPlayerHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $playerRepository = $this->createMock(PlayerRepositoryInterface::class);
        $worldRepository = $this->createMock(WorldRepositoryInterface::class);
        $pendingLevelMessageRepository = $this->createMock(PendingLevelMessageRepositoryInterface::class);

        $levelName = Level1::class;
        $player = new Player('playerId', 'playerName', new Vector(0, 0), new \DateTimeImmutable(), 'worldId', $levelName);
        $world = new World('worldId', 'worldName', [$player]);

        $playerRepository->method('find')->willReturn($player);
        $worldRepository->method('find')->willReturn($world);

        $worldRepository->expects($this->once())->method('save');
        $pendingLevelMessageRepository->expects($this->once())->method('push')->with($world, $levelName);

        $handler = new DisconnectPlayerHandler(
            $playerRepository,
            $worldRepository,
            $pendingLevelMessageRepository
        );

        $handler->__invoke(
            new DisconnectPlayerAsyncMessage($player->id)
        );

        $this->assertSame(0, $world->getPlayersCount());
    }
}
