<?php

namespace App\Game\Application\Service;

use App\Game\Domain\Exception\NotificationException;
use App\Game\Domain\Model\Entity\Level\LevelInterface;
use App\Game\Domain\Model\Entity\World;

interface NotificationGeneratorInterface
{
    /**
     * @throws NotificationException
     */
    public function generateLevelData(World $world, LevelInterface $level): void;

    /**
     * @throws NotificationException
     */
    public function generateExceptionData(string $playerId, string $exceptionClass, string $message): void;

    /**
     * @throws NotificationException
     */
    public function generateMessageData(World $world, LevelInterface $level, string $playerId, string $message): void;
}
