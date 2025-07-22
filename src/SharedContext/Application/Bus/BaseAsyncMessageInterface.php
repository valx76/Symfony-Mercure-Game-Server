<?php

namespace App\SharedContext\Application\Bus;

interface BaseAsyncMessageInterface
{
    public string $playerId { get; }
}
