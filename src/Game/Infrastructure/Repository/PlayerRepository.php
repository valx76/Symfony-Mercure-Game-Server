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
use Psr\Clock\ClockInterface;

final readonly class PlayerRepository implements PlayerRepositoryInterface
{
    public function __construct(
        private DatabaseInterface $database,
        private ClockInterface $clock,
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

        $this->database->setHashValue(
            sprintf(DatabaseKeys::PLAYER_KEY, $player->id),
            DatabaseKeys::PLAYER_LAST_ACTIVITY_TIME,
            $player->lastActivityTime->format('Y-m-d H:i:s')
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

    public function find(string $id): Player
    {
        if (!$this->exists($id)) {
            throw new PlayerNotFoundException('Player not found!');
        }

        $key = sprintf(DatabaseKeys::PLAYER_KEY, $id);

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

            $lastActivityTimeStr = $this->database->getHashValue(
                $key,
                DatabaseKeys::PLAYER_LAST_ACTIVITY_TIME
            );
            $lastActivityTime = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $lastActivityTimeStr);
            $lastActivityTime = (false !== $lastActivityTime) ? $lastActivityTime : $this->clock->now();

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

            return new Player($id, $name, $position, $lastActivityTime, $worldId ?? null, $levelName ?? null);
        } catch (DatabaseFieldNotFoundException|DatabaseKeyNotFoundException $e) {
            throw EntityHasMissingDataException::fromField(Player::class, $e->name);
        } catch (InvalidVectorDataException|VectorNegativeValueException) {
            throw new EntityHasIncorrectDataException();
        }
    }

    public function findAll(): array
    {
        $playerIds = $this->database->findKeysByPattern(
            str_replace('%s', '*', DatabaseKeys::PLAYER_KEY)
        );

        $players = [];
        foreach ($playerIds as $playerIdStr) {
            $playerIdFormat = str_replace('%s', '', DatabaseKeys::PLAYER_KEY);
            $playerId = substr(
                $playerIdStr,
                strpos($playerIdStr, $playerIdFormat) + strlen($playerIdFormat)
            );

            $players[] = $this->find($playerId);
        }

        return $players;
    }

    public function delete(Player $player): void
    {
        try {
            $this->database->deleteKey(
                sprintf(DatabaseKeys::PLAYER_KEY, $player->id)
            );
        } catch (DatabaseKeyNotFoundException) {
            throw new PlayerNotFoundException();
        }
    }

    public function exists(string $id): bool
    {
        $key = sprintf(DatabaseKeys::PLAYER_KEY, $id);

        return $this->database->hasKey($key);
    }
}
