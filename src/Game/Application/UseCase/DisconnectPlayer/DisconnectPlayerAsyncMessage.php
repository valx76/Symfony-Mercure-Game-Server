<?php

namespace App\Game\Application\UseCase\DisconnectPlayer;

use App\SharedContext\Application\Bus\AsyncPlayerMessageInterface;

final readonly class DisconnectPlayerAsyncMessage implements AsyncPlayerMessageInterface
{
    public function __construct(
        public private(set) string $playerId,
    ) {
    }
}
