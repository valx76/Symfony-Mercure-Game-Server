<?php

namespace App\Game\Application\UseCase\MovePlayer;

use App\Game\Application\Bus\AsyncPlayerMessageInterface;

final readonly class MovePlayerAsyncMessage implements AsyncPlayerMessageInterface
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) int $targetX,
        public private(set) int $targetY,
    ) {
    }
}
