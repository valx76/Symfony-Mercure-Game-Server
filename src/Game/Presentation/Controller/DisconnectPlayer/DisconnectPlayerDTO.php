<?php

namespace App\Game\Presentation\Controller\DisconnectPlayer;

use App\Game\Infrastructure\Validator\PlayerExists;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class DisconnectPlayerDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[PlayerExists]
        public private(set) string $playerId,
    ) {
    }
}
