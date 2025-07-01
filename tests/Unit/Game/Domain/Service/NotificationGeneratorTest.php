<?php

namespace App\Tests\Unit\Game\Domain\Service;

use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Service\LevelNormalizerInterface;
use App\Game\Domain\Service\NotificationGenerator;
use App\SharedContext\Application\Mercure\MercurePublisherInterface;
use App\SharedContext\Domain\Model\MercureTopics;
use PHPUnit\Framework\TestCase;

class NotificationGeneratorTest extends TestCase
{
    public function testGeneratesLevelData(): void
    {
        $world = new World('worldId', 'worldName', []);
        $level = new Level1();

        $levelNormalizer = $this->createMock(LevelNormalizerInterface::class);
        $levelNormalizer->method('normalize')->willReturn([]);

        $mercurePublisher = $this->createMock(MercurePublisherInterface::class);
        $mercurePublisher
            ->expects($this->once())
            ->method('publish')
            ->with(
                sprintf(MercureTopics::LEVEL, 'worldId', $level::class),
                '[]',
            );

        $notificationGenerator = new NotificationGenerator($levelNormalizer, $mercurePublisher);
        $notificationGenerator->generateLevelData($world, $level);
    }
}
