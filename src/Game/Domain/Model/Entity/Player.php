<?php

namespace App\Game\Domain\Model\Entity;

use App\SharedContext\Domain\Model\ValueObject\Vector;

final class Player
{
    public function __construct(
        public private(set) readonly string $id,
        public private(set) readonly string $name,
        public Vector $position,
        public ?string $worldId = null,
        public ?string $levelName = null,
    ) {
    }
}
