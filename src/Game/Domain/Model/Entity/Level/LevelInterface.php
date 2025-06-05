<?php

namespace App\Game\Domain\Model\Entity\Level;

use App\SharedContext\Domain\Model\ValueObject\Vector;

interface LevelInterface
{
    /**
     * @return int[]
     */
    public function getTiles(): array;

    public function getSize(): Vector;

    public function getSpawnPosition(): Vector;
}
