<?php

namespace App\Game\Domain\Model\Entity\Level;

use App\SharedContext\Domain\Exception\VectorNegativeValueException;
use App\SharedContext\Domain\Model\ValueObject\Vector;

final readonly class Level2 implements LevelInterface
{
    /**
     * @return int[]
     */
    public function getTiles(): array
    {
        return [
            1, 1, 1, 1, 1, 1, 1,
            1, 0, 0, 0, 0, 0, 1,
            1, 0, 1, 1, 1, 0, 1,
        ];
    }

    /**
     * @throws VectorNegativeValueException
     */
    public function getSize(): Vector
    {
        return new Vector(7, 3);
    }

    /**
     * @throws VectorNegativeValueException
     */
    public function getSpawnPosition(): Vector
    {
        return new Vector(1, 1);
    }
}
