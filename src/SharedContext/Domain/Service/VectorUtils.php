<?php

namespace App\SharedContext\Domain\Service;

use App\Game\Domain\Model\Entity\Level\TileTypeEnum;
use App\SharedContext\Domain\Exception\PositionOutOfAreaException;
use App\SharedContext\Domain\Model\ValueObject\Vector;

class VectorUtils
{
    public static function isVectorInVector(Vector $inner, Vector $outer): bool
    {
        return $inner->x <= $outer->x && $inner->y <= $outer->y;
    }

    /**
     * @param int[] $tiles
     *
     * @throws PositionOutOfAreaException
     */
    public static function isPositionColliding(Vector $position, Vector $size, array $tiles): bool
    {
        if (0 === count($tiles)) {
            return false;
        }

        if ($position->x >= $size->x || $position->y >= $size->y) {
            throw new PositionOutOfAreaException('Incorrect position!');
        }

        $tileValue = $tiles[$position->y * $size->x + $position->x];

        return TileTypeEnum::from($tileValue)->isColliding();
    }
}
