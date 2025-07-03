<?php

namespace App\Game\Application\UseCase\DisconnectPlayer;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\Game\Domain\Service\NotificationGenerator;
use App\SharedContext\Application\Bus\MessageHandler;

final readonly class DisconnectPlayerHandler implements MessageHandler
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private WorldRepositoryInterface $worldRepository,
        private LevelFactory $levelFactory,
        private NotificationGenerator $notificationGenerator,
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
    public function __invoke(DisconnectPlayerAsyncMessage $message): void
    {
        $player = $this->playerRepository->find($message->playerId);

        /** @var ?string $worldId */
        $worldId = $player->worldId;

        if (null !== $worldId) {
            $world = $this->worldRepository->find($worldId);

            $world->removePlayer($player);
            $this->worldRepository->save($world);

            /** @var ?string $levelName */
            $levelName = $player->levelName;

            if (null !== $levelName) {
                $level = $this->levelFactory->create($levelName);

                $this->notificationGenerator->generateLevelData($world, $level);
            }
        }

        $this->playerRepository->delete($player);
    }
}
