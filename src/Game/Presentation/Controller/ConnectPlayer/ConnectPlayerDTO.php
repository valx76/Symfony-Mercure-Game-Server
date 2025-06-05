<?php

namespace App\Game\Presentation\Controller\ConnectPlayer;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ConnectPlayerDTO
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 15)]
        public private(set) string $playerName,
    ) {
    }
}
