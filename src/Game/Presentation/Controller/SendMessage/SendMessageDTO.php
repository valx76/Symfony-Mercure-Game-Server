<?php

namespace App\Game\Presentation\Controller\SendMessage;

use App\Game\Infrastructure\Validator\PlayerExists;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class SendMessageDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[PlayerExists]
        public private(set) string $playerId,
        public private(set) string $message,
    ) {
    }
}
