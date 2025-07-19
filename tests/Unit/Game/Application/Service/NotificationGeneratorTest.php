<?php

namespace App\Tests\Unit\Game\Application\Service;

use App\Game\Application\Service\NotificationGenerator;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Service\LevelNormalizerInterface;
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

    public function testGeneratesExceptionData(): void
    {
        $playerId = 'testPlayer';
        $exceptionClass = 'testException';
        $message = 'testMessage';

        $data = json_encode([
            'MESSAGE' => $message,
            'EXCEPTION' => $exceptionClass,
        ], JSON_THROW_ON_ERROR);

        $levelNormalizer = $this->createMock(LevelNormalizerInterface::class);

        $mercurePublisher = $this->createMock(MercurePublisherInterface::class);
        $mercurePublisher
            ->expects($this->once())
            ->method('publish')
            ->with(
                sprintf(MercureTopics::PLAYER, $playerId),
                $data,
            );

        $notificationGenerator = new NotificationGenerator($levelNormalizer, $mercurePublisher);
        $notificationGenerator->generateExceptionData($playerId, $exceptionClass, $message);
    }

    public function testGeneratesMessageData(): void
    {
        $playerId = 'testPlayer';
        $message = 'testMessage';

        $world = new World('worldId', 'worldName', []);
        $level = new Level1();

        $levelNormalizer = $this->createMock(LevelNormalizerInterface::class);
        $levelNormalizer->method('normalize')->willReturn([]);

        $mercurePublisher = $this->createMock(MercurePublisherInterface::class);
        $mercurePublisher
            ->expects($this->once())
            ->method('publish')
            ->with(
                sprintf(MercureTopics::MESSAGE, 'worldId', $level::class),
                json_encode([
                    'PLAYER' => $playerId,
                    'MESSAGE' => $message,
                ]),
            );

        $notificationGenerator = new NotificationGenerator($levelNormalizer, $mercurePublisher);
        $notificationGenerator->generateMessageData($world, $level, $playerId, $message);
    }
}
