<?php

namespace App\Game\Domain\Model\Entity\Level;

use App\SharedContext\Domain\Model\ValueObject\Vector;

final readonly class TeleportPosition
{
    public function __construct(
        public private(set) Vector $currentLevelPosition,
        public private(set) string $targetLevelName,
        public private(set) Vector $targetLevelPosition,
    ) {
    }
}
