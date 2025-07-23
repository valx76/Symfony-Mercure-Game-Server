<?php

namespace App\Game\Application\UseCase\PublishPendingMessage;

use App\Game\Application\Service\NotificationGenerator;
use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\SharedContext\Application\Bus\MessageHandlerInterface;

final readonly class PublishPendingMessageHandler implements MessageHandlerInterface
{
    public function __construct(
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
     */
    public function __invoke(PublishPendingMessageAsyncMessage $message): void
    {
        $world = $this->worldRepository->find($message->worldId);
        $level = $this->levelFactory->create($message->levelName);

        $this->notificationGenerator->generateLevelData($world, $level);
    }
}
