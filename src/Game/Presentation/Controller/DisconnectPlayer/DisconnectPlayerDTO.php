<?php

namespace App\Game\Presentation\Controller\DisconnectPlayer;

final readonly class DisconnectPlayerDTO
{
    public function __construct(
        public private(set) string $playerId,
    ) {
    }
}
