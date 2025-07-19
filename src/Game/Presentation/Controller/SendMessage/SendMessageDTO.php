<?php

namespace App\Game\Presentation\Controller\SendMessage;

final readonly class SendMessageDTO
{
    public function __construct(
        public private(set) string $playerId,
        public private(set) string $message,
    ) {
    }
}
