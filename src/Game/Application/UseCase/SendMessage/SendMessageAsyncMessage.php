<?php

namespace App\Game\Application\UseCase\SendMessage;

use App\SharedContext\Application\Bus\AsyncChatMessageInterface;

final readonly class SendMessageAsyncMessage implements AsyncChatMessageInterface
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) string $message,
    ) {
    }
}
