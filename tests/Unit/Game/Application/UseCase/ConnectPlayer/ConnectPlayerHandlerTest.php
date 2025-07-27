<?php

namespace App\Tests\Unit\Game\Application\UseCase\ConnectPlayer;

use App\Game\Application\UseCase\ConnectPlayer\ConnectPlayerHandler;
use App\Game\Application\UseCase\ConnectPlayer\ConnectPlayerSyncMessage;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\AvailableWorldFinderInterface;
use App\Game\Domain\Service\LevelFactory;
use App\Game\Domain\Service\LevelNormalizerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class ConnectPlayerHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $playerRepository = $this->createMock(PlayerRepositoryInterface::class);
        $worldRepository = $this->createMock(WorldRepositoryInterface::class);
        $pendingLevelMessageRepository = $this->createMock(PendingLevelMessageRepositoryInterface::class);
        $availableWorldFinder = $this->createMock(AvailableWorldFinderInterface::class);
        $levelNormalizer = $this->createMock(LevelNormalizerInterface::class);
        $clock = $this->createMock(ClockInterface::class);

        $levelFactory = new LevelFactory();
        $levelName = Level1::class;
        $level = $levelFactory->create($levelName);

        $world = new World('worldId', 'worldName', []);
        $availableWorldFinder->method('find')->willReturn($world);

        $worldRepository->expects($this->once())->method('save');
        $playerRepository->expects($this->once())->method('save');
        $pendingLevelMessageRepository->expects($this->once())->method('push')->with($world, $levelName);
        $levelNormalizer->expects($this->once())->method('normalize')->with($world, $level)->willReturn([]);

        $handler = new ConnectPlayerHandler(
            $playerRepository,
            $worldRepository,
            $pendingLevelMessageRepository,
            $availableWorldFinder,
            $levelFactory,
            $levelNormalizer,
            $clock,
            $levelName
        );

        $result = $handler->__invoke(
            new ConnectPlayerSyncMessage('playerId', 'playerName')
        );

        $this->assertSame(1, $world->getPlayersCount());
        $this->assertSame('playerId', $result->playerId);
        $this->assertSame($world->id, $result->worldId);
        $this->assertSame([], $result->levelData);
    }
}
