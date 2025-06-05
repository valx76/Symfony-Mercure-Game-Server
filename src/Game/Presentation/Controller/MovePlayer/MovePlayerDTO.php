<?php

namespace App\Game\Presentation\Controller\MovePlayer;

final readonly class MovePlayerDTO
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) int $targetX,
        public private(set) int $targetY,
    ) {
    }
}
