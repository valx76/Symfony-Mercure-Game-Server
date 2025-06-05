<?php

namespace App\Game\Application\UseCase\ConnectPlayer;

final readonly class ConnectPlayerResult
{
    /** @param array<string, int|string|array<int|array<string, int|string>>> $levelData */
    public function __construct(
        public private(set) string $playerId,
        public private(set) string $worldId,
        public private(set) array $levelData,
    ) {
    }
}
