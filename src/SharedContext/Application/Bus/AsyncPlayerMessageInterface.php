<?php

namespace App\SharedContext\Application\Bus;

interface AsyncPlayerMessageInterface
{
    public string $playerId { get; }
}
