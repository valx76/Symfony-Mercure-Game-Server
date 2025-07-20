<?php

namespace App\Game\Presentation\Controller\MovePlayer;

use App\Game\Infrastructure\Validator\PlayerExists;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class MovePlayerDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[PlayerExists]
        public private(set) string $playerId,
        public private(set) int $targetX,
        public private(set) int $targetY,
    ) {
    }
}
