<?php

namespace App\Tests\Unit\Game\Application\UseCase\SendMessage;

use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Application\UseCase\SendMessage\SendMessageAsyncMessage;
use App\Game\Application\UseCase\SendMessage\SendMessageHandler;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;

class SendMessageHandlerTest extends TestCase
{
    public function testInvoke(): void
    {
        $playerRepository = $this->createMock(PlayerRepositoryInterface::class);
        $worldRepository = $this->createMock(WorldRepositoryInterface::class);
        $notificationGenerator = $this->createMock(NotificationGeneratorInterface::class);

        $levelFactory = new LevelFactory();
        $levelName = Level1::class;
        $level = $levelFactory->create($levelName);

        $player = new Player('playerId', 'playerName', new Vector(0, 0), new \DateTimeImmutable(), 'worldId', $levelName);
        $world = new World('worldId', 'worldName', [$player]);
        $message = 'Hello there!';

        $playerRepository->method('find')->willReturn($player);
        $worldRepository->method('find')->willReturn($world);

        $notificationGenerator->expects($this->once())->method('generateMessageData')->with($world, $level, $player->id, $message);

        $handler = new SendMessageHandler(
            $playerRepository,
            $worldRepository,
            $levelFactory,
            $notificationGenerator
        );

        $handler->__invoke(
            new SendMessageAsyncMessage($player->id, $message)
        );
    }
}
