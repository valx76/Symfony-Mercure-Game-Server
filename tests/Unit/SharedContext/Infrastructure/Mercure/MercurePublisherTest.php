<?php

namespace App\Tests\Unit\SharedContext\Infrastructure\Mercure;

use App\SharedContext\Infrastructure\Mercure\MercurePublisher;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\MockHub;
use Symfony\Component\Mercure\Update;

class MercurePublisherTest extends TestCase
{
    public function testPublishes(): void
    {
        $topic = 'topic';
        $data = 'data';

        $hub = new MockHub('https://internal/.well-known/mercure', new StaticTokenProvider('test'), function (Update $update) use ($topic, $data): string {
            $this->assertSame($topic, $update->getTopics()[0]);
            $this->assertSame($data, $update->getData());

            return 'id';
        });

        $mercurePublisher = new MercurePublisher($hub);
        $mercurePublisher->publish($topic, $data);
    }
}
