<?php

namespace App\Game\Application\UseCase\SendMessage;

use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\SharedContext\Application\Bus\MessageHandlerInterface;
use Psr\Clock\ClockInterface;

final readonly class SendMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private WorldRepositoryInterface $worldRepository,
        private LevelFactory $levelFactory,
        private NotificationGeneratorInterface $notificationGenerator,
        private ClockInterface $clock,
    ) {
    }

    /**
     * @throws EntityHasIncorrectDataException
     * @throws WorldNotFoundException
     * @throws LevelNotFoundException
     * @throws NotificationException
     * @throws EntityHasMissingDataException
     * @throws PlayerNotFoundException
     */
    public function __invoke(SendMessageAsyncMessage $message): void
    {
        $player = $this->playerRepository->find($message->playerId);

        /** @var ?string $worldId */
        $worldId = $player->worldId;

        if (null !== $worldId) {
            $world = $this->worldRepository->find($worldId);

            /** @var ?string $levelName */
            $levelName = $player->levelName;

            if (null !== $levelName) {
                $level = $this->levelFactory->create($levelName);

                $this->notificationGenerator->generateMessageData($world, $level, $message->playerId, $message->message);

                $player->lastActivityTime = $this->clock->now();
                $this->playerRepository->save($player);
            }
        }
    }
}
