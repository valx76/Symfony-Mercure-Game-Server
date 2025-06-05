<?php

namespace App\Game\Domain\Model\Repository;

use App\Game\Domain\Exception\EntityHasIncorrectDataException;
use App\Game\Domain\Exception\EntityHasMissingDataException;
use App\Game\Domain\Exception\WorldNotFoundException;
use App\Game\Domain\Model\Entity\World;

interface WorldRepositoryInterface
{
    public function save(World $world): void;

    /**
     * @throws WorldNotFoundException
     * @throws EntityHasMissingDataException
     * @throws EntityHasIncorrectDataException
     */
    public function find(string $id): World;

    /**
     * @return World[]
     */
    public function findAll(): array;
}
