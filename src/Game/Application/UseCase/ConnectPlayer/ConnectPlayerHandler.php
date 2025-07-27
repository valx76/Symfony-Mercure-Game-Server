<?php

namespace App\Game\Application\UseCase\ConnectPlayer;

use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Exception\NoWorldAvailableException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PendingLevelMessageRepositoryInterface;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\AvailableWorldFinderInterface;
use App\Game\Domain\Service\LevelFactory;
use App\Game\Domain\Service\LevelNormalizerInterface;
use App\SharedContext\Application\Bus\MessageHandlerInterface;
use Psr\Clock\ClockInterface;

final readonly class ConnectPlayerHandler implements MessageHandlerInterface
{
    public function __construct(
        private PlayerRepositoryInterface $playerRepository,
        private WorldRepositoryInterface $worldRepository,
        private PendingLevelMessageRepositoryInterface $pendingLevelMessageRepository,
        private AvailableWorldFinderInterface $availableWorldFinder,
        private LevelFactory $levelFactory,
        private LevelNormalizerInterface $levelNormalizer,
        private ClockInterface $clock,
        private string $defaultLevelName,
    ) {
    }

    /**
     * @throws LevelNotFoundException
     * @throws NoWorldAvailableException
     */
    public function __invoke(ConnectPlayerSyncMessage $message): ConnectPlayerResult
    {
        $world = $this->availableWorldFinder->find();

        $level = $this->levelFactory->create($this->defaultLevelName);

        $player = new Player(
            $message->playerId,
            $message->playerName,
            $level->getSpawnPosition(),
            $this->clock->now(),
            $world->id,
            $level::class
        );

        $world->addPlayer($player);

        // TODO - Check the below: if it fails, we potentially keep corrupted data in Redis
        // -> See exceptions that can be thrown in Redis when using 'hset' and bubble up to here!
        // --> Then try/catch and remove player from World, and remove Player
        $this->worldRepository->save($world);
        $this->playerRepository->save($player);

        $this->pendingLevelMessageRepository->push($world, $level::class);

        return new ConnectPlayerResult(
            $player->id,
            $world->id,
            $this->levelNormalizer->normalize($world, $level)
        );
    }
}
