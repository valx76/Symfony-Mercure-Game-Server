<?php

namespace App\Game\Infrastructure\Repository;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\SharedContext\Application\Database\DatabaseInterface;
use App\SharedContext\Domain\Exception\DatabaseFieldNotFoundException;
use App\SharedContext\Domain\Exception\DatabaseKeyNotFoundException;
use App\SharedContext\Domain\Exception\InvalidVectorDataException;
use App\SharedContext\Domain\Exception\VectorNegativeValueException;
use App\SharedContext\Domain\Model\DatabaseKeys;
use App\SharedContext\Domain\Model\ValueObject\Vector;

final readonly class PlayerRepository implements PlayerRepositoryInterface
{
    public function __construct(
        private DatabaseInterface $database,
    ) {
    }

    public function save(Player $player): void
    {
        $this->database->setHashValue(
            sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
            DatabaseKeys::PLAYER_NAME,
            $player->name
        );

        $this->database->setHashValue(
            sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
            DatabaseKeys::PLAYER_POSITION,
            (string) $player->position
        );

        if (null !== $player->worldId) {
            $this->database->setHashValue(
                sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
                DatabaseKeys::PLAYER_WORLD,
                $player->worldId
            );
        }

        if (null !== $player->levelName) {
            $this->database->setHashValue(
                sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
                DatabaseKeys::PLAYER_LEVEL,
                $player->levelName
            );
        }
    }

    /**
     * @throws PlayerNotFoundException
     * @throws EntityHasMissingDataException
     * @throws EntityHasIncorrectDataException
     */
    public function find(string $id): Player
    {
        $key = sprintf(DatabaseKeys::PLAYER_KEY, $id);

        if (!$this->database->hasKey($key)) {
            throw new PlayerNotFoundException();
        }

        try {
            $name = $this->database->getHashValue(
                $key,
                DatabaseKeys::PLAYER_NAME
            );

            $positionStr = $this->database->getHashValue(
                $key,
                DatabaseKeys::PLAYER_POSITION
            );
            $position = Vector::fromString($positionStr);

            if ($this->database->hasHashField($key, DatabaseKeys::PLAYER_WORLD)) {
                $worldId = $this->database->getHashValue(
                    $key,
                    DatabaseKeys::PLAYER_WORLD
                );
            }

            if ($this->database->hasHashField($key, DatabaseKeys::PLAYER_LEVEL)) {
                $levelName = $this->database->getHashValue(
                    $key,
                    DatabaseKeys::PLAYER_LEVEL
                );
            }

            return new Player($id, $name, $position, $worldId ?? null, $levelName ?? null);
        } catch (DatabaseFieldNotFoundException|DatabaseKeyNotFoundException $e) {
            throw EntityHasMissingDataException::fromField(Player::class, $e->name);
        } catch (InvalidVectorDataException|VectorNegativeValueException) {
            throw new EntityHasIncorrectDataException();
        }
    }
}
