<?php

namespace App\Game\Application\UseCase\SendMessage;

use App\SharedContext\Application\Bus\AsyncPlayerMessageInterface;

final readonly class SendMessageAsyncMessage implements AsyncPlayerMessageInterface
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) string $message,
    ) {
    }
}
