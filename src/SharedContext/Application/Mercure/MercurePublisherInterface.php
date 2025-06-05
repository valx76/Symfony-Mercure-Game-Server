<?php

namespace App\SharedContext\Application\Mercure;

interface MercurePublisherInterface
{
    public function publish(string $topic, string $data): void;
}
