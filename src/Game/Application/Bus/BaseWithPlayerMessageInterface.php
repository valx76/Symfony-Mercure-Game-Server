<?php

namespace App\Game\Application\Bus;

use App\SharedContext\Application\Bus\MessageInterface;

interface BaseWithPlayerMessageInterface extends MessageInterface
{
    public string $playerId { get; }
}
