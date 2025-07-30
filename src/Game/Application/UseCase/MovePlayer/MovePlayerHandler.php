<?php

namespace App\Game\Application\UseCase\MovePlayer;

use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Exception\PlayerNotInLevelException;
use App\Game\Domain\Exception\PlayerNotInWorldException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Entity\Level\LevelInterface;
use App\Game\Domain\Model\Entity\Level\TeleportPosition;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\SharedContext\Application\Bus\MessageHandlerInterface;
use App\SharedContext\Domain\Exception\PositionCollidingException;
use App\SharedContext\Domain\Exception\PositionOutOfAreaException;
use App\SharedContext\Domain\Exception\VectorNegativeValueException;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use App\SharedContext\Domain\Service\VectorUtils;
use Psr\Clock\ClockInterface;

final readonly class MovePlayerHandler implements MessageHandlerInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private WorldRepositoryInterface $worldRepository,
        private PendingLevelMessageRepositoryInterface $pendingLevelMessageRepository,
        private LevelFactory $levelFactory,
        private ClockInterface $clock,
        private NotificationGeneratorInterface $notificationGenerator,
    ) {
    }

    /**
     * @throws EntityHasIncorrectDataException
     * @throws WorldNotFoundException
     * @throws LevelNotFoundException
     * @throws EntityHasMissingDataException
     * @throws PlayerNotFoundException
     * @throws VectorNegativeValueException
     * @throws PositionOutOfAreaException
     * @throws PositionCollidingException
     * @throws PlayerNotInLevelException
     * @throws PlayerNotInWorldException
     * @throws NotificationException
     */
    public function __invoke(MovePlayerAsyncMessage $message): void
    {
        $player = $this->playerRepository->find($message->playerId);

        /** @var string $levelName */
        $levelName = $player->levelName ?? throw new PlayerNotInLevelException('Player not in a level!');
        $level = $this->levelFactory->create($levelName);

        /** @var string $worldId */
        $worldId = $player->worldId ?? throw new PlayerNotInWorldException('Player not in a world!');
        $world = $this->worldRepository->find($worldId);

        $targetPosition = new Vector($message->targetX, $message->targetY);

        $this->validatePosition($targetPosition, $level);

        /** @var ?TeleportPosition $teleportPosition */
        $teleportPosition = array_find(
            $level->getTeleportPositions(),
            fn (TeleportPosition $position) => $position->currentLevelPosition->equals($targetPosition)
        );

        if (null !== $teleportPosition) {
            $this->teleportPlayer($player, $world, $teleportPosition);
        } else {
            $this->updatePlayer($player, $player->levelName, $targetPosition);
        }

        $this->pendingLevelMessageRepository->push($world, $levelName);
    }

    /**
     * @throws PositionCollidingException
     * @throws PositionOutOfAreaException
     */
    private function validatePosition(Vector $position, LevelInterface $level): void
    {
        if (!VectorUtils::isVectorInVector($position, $level->getSize())) {
            throw new PositionOutOfAreaException('Incorrect position!');
        }

        if (VectorUtils::isPositionColliding($position, $level->getSize(), $level->getTiles())) {
            throw new PositionCollidingException('Cannot move to this position!');
        }
    }

    /**
     * @throws NotificationException
     */
    private function teleportPlayer(Player $player, World $world, TeleportPosition $teleportPosition): void
    {
        $targetLevelName = $teleportPosition->targetLevelName;

        if ($targetLevelName !== $player->levelName) {
            $this->updatePlayer($player, $targetLevelName, $teleportPosition->targetLevelPosition);

            $this->pendingLevelMessageRepository->push($world, $targetLevelName);

            $this->notificationGenerator->generateLevelChangeData($player->id, $targetLevelName);
        }
    }

    private function updatePlayer(Player $player, string $targetLevelName, Vector $targetPosition): void
    {
        $player->levelName = $targetLevelName;
        $player->position = $targetPosition;
        $player->lastActivityTime = $this->clock->now();
        $this->playerRepository->save($player);
    }
}
