<?php

namespace App\SharedContext\Infrastructure\Mercure;

use App\SharedContext\Application\Mercure\MercurePublisherInterface;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

final readonly class MercurePublisher implements MercurePublisherInterface
{
    public function __construct(
        private HubInterface $hub,
    ) {
    }

    public function publish(string $topic, string $data): void
    {
        $update = new Update(
            $topic,
            $data
        );

        $this->hub->publish($update);
    }
}
