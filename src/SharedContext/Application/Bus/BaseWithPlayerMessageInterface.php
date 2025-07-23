<?php

namespace App\SharedContext\Application\Bus;

interface BaseWithPlayerMessageInterface
{
    public string $playerId { get; }
}
