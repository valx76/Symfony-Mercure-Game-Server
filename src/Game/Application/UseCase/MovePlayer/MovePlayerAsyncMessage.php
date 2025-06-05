<?php

namespace App\Game\Application\UseCase\MovePlayer;

use App\SharedContext\Application\Bus\AsyncMessageInterface;

final readonly class MovePlayerAsyncMessage implements AsyncMessageInterface
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) int $targetX,
        public private(set) int $targetY,
    ) {
    }
}
