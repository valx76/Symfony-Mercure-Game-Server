<?php

namespace App\Game\Application\UseCase\ConnectPlayer;

use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Exception\NoWorldAvailableException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\AvailableWorldFinder;
use App\Game\Domain\Service\LevelFactory;
use App\Game\Domain\Service\LevelNormalizer;
use App\Game\Domain\Service\NotificationGenerator;
use App\SharedContext\Application\Bus\MessageHandler;

final readonly class ConnectPlayerHandler implements MessageHandler
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private WorldRepositoryInterface $worldRepository,
        private AvailableWorldFinder $availableWorldFinder,
        private LevelFactory $levelFactory,
        private LevelNormalizer $levelNormalizer,
        private NotificationGenerator $notificationGenerator,
        private string $defaultLevelName,
    ) {
    }

    /**
     * @throws LevelNotFoundException
     * @throws NoWorldAvailableException
     * @throws NotificationException
     */
    public function __invoke(ConnectPlayerSyncMessage $message): ConnectPlayerResult
    {
        $world = $this->availableWorldFinder->find();

        $level = $this->levelFactory->create($this->defaultLevelName);

        $player = new Player(
            $message->playerId,
            $message->playerName,
            $level->getSpawnPosition(),
            $world->id,
            $level::class
        );

        $world->addPlayer($player);
        $this->worldRepository->save($world);
        $this->playerRepository->save($player);

        $this->notificationGenerator->generateLevelData($world, $level);

        return new ConnectPlayerResult(
            $player->id,
            $world->id,
            $this->levelNormalizer->normalize($world, $level)
        );
    }
}
