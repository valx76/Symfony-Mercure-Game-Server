<?php

namespace App\Game\Domain\Model\Entity\Level;

enum TileTypeEnum: int
{
    case EMPTY = 0;
    case WALL = 1;

    public function isColliding(): bool
    {
        return match ($this) {
            self::EMPTY => false,
            self::WALL => true,
            default => throw new \ValueError('Unknown tile type'),
        };
    }
}
