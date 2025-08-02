<?php

namespace App\Game\Application\UseCase\ConnectPlayer;

use App\Game\Application\Bus\SyncMessageInterface;

final readonly class ConnectPlayerSyncMessage implements SyncMessageInterface
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) string $playerName,
    ) {
    }
}
