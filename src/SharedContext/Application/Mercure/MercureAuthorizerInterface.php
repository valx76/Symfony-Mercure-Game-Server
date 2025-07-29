<?php

namespace App\SharedContext\Application\Mercure;

interface MercureAuthorizerInterface
{
    public function authorize(string $playerId, string $worldId): void;
}
