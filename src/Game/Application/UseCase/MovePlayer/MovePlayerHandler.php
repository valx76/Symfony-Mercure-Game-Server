<?php

namespace App\Game\Application\UseCase\MovePlayer;

use App\Game\Application\Service\NotificationGenerator;
use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Exception\PlayerNotInLevelException;
use App\Game\Domain\Exception\PlayerNotInWorldException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Entity\Level\TeleportPosition;
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
        private LevelFactory $levelFactory,
        private NotificationGenerator $notificationGenerator,
        private ClockInterface $clock,
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
        // TODO - Refactor this method

        $player = $this->playerRepository->find($message->playerId);

        /** @var ?string $levelName */
        $levelName = $player->levelName;

        /** @var ?string $worldId */
        $worldId = $player->worldId;

        if (null === $levelName) {
            throw new PlayerNotInLevelException('Player not in a level!');
        }

        if (null === $worldId) {
            throw new PlayerNotInWorldException('Player not in a world!');
        }

        $level = $this->levelFactory->create($levelName);
        $targetPosition = new Vector($message->targetX, $message->targetY);

        if (!VectorUtils::isVectorInVector($targetPosition, $level->getSize())) {
            throw new PositionOutOfAreaException('Incorrect position!');
        }

        if (VectorUtils::isPositionColliding($targetPosition, $level->getSize(), $level->getTiles())) {
            throw new PositionCollidingException('Cannot move to this position!');
        }

        $targetLevel = $level;
        $targetLevelName = $levelName;

        /** @var ?TeleportPosition $teleportPosition */
        $teleportPosition = array_find(
            $level->getTeleportPositions(),
            fn (TeleportPosition $position) => $position->currentLevelPosition->equals($targetPosition)
        );

        if (null !== $teleportPosition) {
            $targetLevelName = $teleportPosition->targetLevelName;
            $targetLevel = $this->levelFactory->create($targetLevelName);

            $player->levelName = $teleportPosition->targetLevelName;
            $player->position = $teleportPosition->targetLevelPosition;
        } else {
            $player->position = $targetPosition;
        }

        $player->lastActivityTime = $this->clock->now();
        $this->playerRepository->save($player);

        $world = $this->worldRepository->find($worldId);

        if ($targetLevelName !== $levelName) {
            $this->notificationGenerator->generateLevelData($world, $targetLevel);
        }

        $this->notificationGenerator->generateLevelData($world, $level);
    }
}
