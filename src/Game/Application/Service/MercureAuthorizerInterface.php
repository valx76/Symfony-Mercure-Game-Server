<?php

namespace App\Game\Application\Service;

interface MercureAuthorizerInterface
{
    public function authorize(string $playerId, string $worldId): void;
}
