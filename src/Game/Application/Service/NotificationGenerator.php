<?php

namespace App\Game\Application\Service;

use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Model\Entity\Level\LevelInterface;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Service\LevelNormalizerInterface;
use App\SharedContext\Application\Mercure\MercurePublisherInterface;
use App\SharedContext\Domain\Model\MercureTopics;

final readonly class NotificationGenerator
{
    public function __construct(
        private LevelNormalizerInterface $levelNormalizer,
        private MercurePublisherInterface $publisher,
    ) {
    }

    /**
     * @throws NotificationException
     */
    public function generateLevelData(World $world, LevelInterface $level): void
    {
        try {
            $data = json_encode($this->levelNormalizer->normalize($world, $level), JSON_THROW_ON_ERROR);

            $this->publisher->publish(
                sprintf(MercureTopics::LEVEL, $world->id, $level::class),
                $data
            );
        } catch (\Throwable) {
            throw new NotificationException('Failed to generate level data!');
        }
    }

    /**
     * @throws NotificationException
     */
    public function generateExceptionData(string $playerId, string $exceptionClass, string $message): void
    {
        try {
            $data = json_encode([
                'MESSAGE' => $message,
                'EXCEPTION' => $exceptionClass,
            ], JSON_THROW_ON_ERROR);

            $this->publisher->publish(
                sprintf(MercureTopics::PLAYER, $playerId),
                $data
            );
        } catch (\Throwable) {
            throw new NotificationException('Failed to generate exception data!');
        }
    }
}
