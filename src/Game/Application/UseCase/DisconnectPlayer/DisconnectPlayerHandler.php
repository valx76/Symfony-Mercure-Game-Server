<?php

namespace App\Game\Application\UseCase\DisconnectPlayer;

use App\Game\Application\Service\NotificationGeneratorInterface;
use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\SharedContext\Application\Bus\MessageHandlerInterface;

final readonly class DisconnectPlayerHandler implements MessageHandlerInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private WorldRepositoryInterface $worldRepository,
        private PendingLevelMessageRepositoryInterface $pendingLevelMessageRepository,
        private NotificationGeneratorInterface $notificationGenerator,
    ) {
    }

    /**
     * @throws EntityHasIncorrectDataException
     * @throws WorldNotFoundException
     * @throws EntityHasMissingDataException
     * @throws PlayerNotFoundException
     * @throws NotificationException
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
                $this->pendingLevelMessageRepository->push($world, $levelName);
            }
        }

        $this->playerRepository->delete($player);

        $this->notificationGenerator->generateDisconnectData($message->playerId);
    }
}
