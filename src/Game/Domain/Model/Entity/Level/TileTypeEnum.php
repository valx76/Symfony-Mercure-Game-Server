<?php

namespace App\Game\Domain\Model\Entity\Level;

enum TileTypeEnum: int
{
    case EMPTY = 0;
    case WALL = 1;
    case TELEPORTER = 2;

    public function isColliding(): bool
    {
        return match ($this) {
            self::EMPTY, self::TELEPORTER => false,
            self::WALL => true,
            // default => throw new \ValueError('Unknown tile type'),
        };
    }
}
