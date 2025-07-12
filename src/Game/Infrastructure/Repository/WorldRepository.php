<?php

namespace App\Game\Infrastructure\Repository;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\SharedContext\Application\Database\DatabaseInterface;
use App\SharedContext\Domain\Exception\DatabaseFieldNotFoundException;
use App\SharedContext\Domain\Exception\DatabaseKeyNotFoundException;
use App\SharedContext\Domain\Model\DatabaseKeys;

final readonly class WorldRepository implements WorldRepositoryInterface
{
    public function __construct(
        private DatabaseInterface $database,
        private PlayerRepositoryInterface $playerRepository,
    ) {
    }

    public function save(World $world): void
    {
        $this->database->setHashValue(
            sprintf(DatabaseKeys::WORLD_KEY, $world->id),
            DatabaseKeys::WORLD_NAME,
            $world->name
        );

        $this->database->setHashValue(
            sprintf(DatabaseKeys::WORLD_KEY, $world->id),
            DatabaseKeys::WORLD_PLAYERS,
            $this->formatPlayersFromWorld($world)
        );
    }

    /**
     * @throws WorldNotFoundException
     * @throws EntityHasMissingDataException
     * @throws EntityHasIncorrectDataException
     */
    public function find(string $id): World
    {
        $key = sprintf(DatabaseKeys::WORLD_KEY, $id);

        if (!$this->database->hasKey($key)) {
            throw new WorldNotFoundException('World not found!');
        }

        try {
            $name = $this->database->getHashValue(
                $key,
                DatabaseKeys::WORLD_NAME
            );

            $players = [];
            $playerIds = $this->database->getHashValue(
                $key,
                DatabaseKeys::WORLD_PLAYERS
            );
            foreach (explode(',', $playerIds) as $playerId) {
                if (0 === strlen(trim($playerId))) {
                    continue;
                }

                $players[] = $this->playerRepository->find($playerId);
            }

            return new World($id, $name, $players);
        } catch (DatabaseFieldNotFoundException|DatabaseKeyNotFoundException $e) {
            throw EntityHasMissingDataException::fromField(World::class, $e->name);
        } catch (PlayerNotFoundException) {
            throw new EntityHasIncorrectDataException();
        }
    }

    /**
     * @return World[]
     */
    public function findAll(): array
    {
        $worlds = [];

        $worldIds = $this->database->findKeysByPattern(
            str_replace('%s', '*', DatabaseKeys::WORLD_KEY)
        );

        foreach ($worldIds as $worldIdStr) {
            $worldIdFormat = str_replace('%s', '', DatabaseKeys::WORLD_KEY);
            $worldId = substr(
                $worldIdStr,
                strpos($worldIdStr, $worldIdFormat) + strlen($worldIdFormat)
            );

            try {
                $worlds[] = $this->find($worldId);
            } catch (WorldNotFoundException|EntityHasIncorrectDataException|EntityHasMissingDataException) {
            }
        }

        return $worlds;
    }

    private function formatPlayersFromWorld(World $world): string
    {
        $playerIds = [];

        foreach ($world->getPlayers() as $player) {
            $playerIds[] = $player->id;
        }

        return implode(',', $playerIds);
    }
}
