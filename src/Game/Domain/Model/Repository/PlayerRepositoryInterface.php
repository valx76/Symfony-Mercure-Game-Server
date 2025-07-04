<?php

namespace App\Game\Domain\Model\Repository;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\PlayerNotFoundException;
use App\Game\Domain\Model\Entity\Player;

interface PlayerRepositoryInterface
{
    public function save(Player $player): void;

    /**
     * @throws PlayerNotFoundException
     * @throws EntityHasMissingDataException
     * @throws EntityHasIncorrectDataException
     */
    public function find(string $id): Player;

    /**
     * @return Player[]
     *
     * @throws EntityHasMissingDataException
     * @throws EntityHasIncorrectDataException
     * @throws PlayerNotFoundException
     */
    public function findAll(): array;

    /**
     * @throws PlayerNotFoundException
     */
    public function delete(Player $player): void;
}
