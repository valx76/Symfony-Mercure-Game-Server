<?php

namespace App\Tests\Unit\Game\Domain\Service;

use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Service\TimeoutPlayerFinder;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;
use Psr\Clock\ClockInterface;

class TimeoutPlayerFinderTest extends TestCase
{
    private const int PLAYER_INACTIVITY_TIMEOUT_SECS = 60;

    public function testFindsCorrectWorld(): void
    {
        $playerRepository = $this->createMock(PlayerRepositoryInterface::class);
        $playerRepository->method('findAll')->willReturn([
            new Player('player1', '', new Vector(0, 0), new \DateTimeImmutable()),
            new Player('player2', '', new Vector(0, 0), new \DateTimeImmutable()->sub(new \DateInterval('PT30S'))),
            new Player('player3', '', new Vector(0, 0), new \DateTimeImmutable()->sub(new \DateInterval('PT59S'))),
            new Player('player4', '', new Vector(0, 0), new \DateTimeImmutable()->sub(new \DateInterval('PT60S'))),
            new Player('player5', '', new Vector(0, 0), new \DateTimeImmutable()->sub(new \DateInterval('PT65S'))),
        ]);

        $clock = $this->createMock(ClockInterface::class);
        $clock->method('now')->willReturn(new \DateTimeImmutable());

        $timeoutPlayerFinder = new TimeoutPlayerFinder($playerRepository, $clock, self::PLAYER_INACTIVITY_TIMEOUT_SECS);
        $timeoutPlayers = $timeoutPlayerFinder->find();

        $this->assertCount(2, $timeoutPlayers);
        $this->assertSame('player4', $timeoutPlayers[0]->id);
        $this->assertSame('player5', $timeoutPlayers[1]->id);
    }
}
