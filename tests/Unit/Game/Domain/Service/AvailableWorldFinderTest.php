<?php

namespace App\Tests\Unit\Game\Domain\Service;

use App\Game\Domain\Exception\NoWorldAvailableException;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\Game\Domain\Service\AvailableWorldFinder;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;

class AvailableWorldFinderTest extends TestCase
{
    public const int MAX_PLAYERS_PER_WORLD = 3;

    public function testFindsCorrectWorld(): void
    {
        $fullWorld = $this->createFullWorld();
        $availableWorld = $this->createAvailableWorld();

        $worldRepository = $this->createMock(WorldRepositoryInterface::class);

        $worldRepository->method('findAll')->willReturn([
            $fullWorld,
            $availableWorld,
        ]);
        $availableWorldFinder = new AvailableWorldFinder($worldRepository, self::MAX_PLAYERS_PER_WORLD);

        $this->assertSame($availableWorld, $availableWorldFinder->find());
    }

    public function testThrowExceptionWhenNoWorldAvailable(): void
    {
        $worldRepository = $this->createMock(WorldRepositoryInterface::class);
        $worldRepository->method('findAll')->willReturn([
            $this->createFullWorld(),
        ]);
        $availableWorldFinder = new AvailableWorldFinder($worldRepository, self::MAX_PLAYERS_PER_WORLD);

        $this->expectException(NoWorldAvailableException::class);
        $availableWorldFinder->find();
    }

    private function createFullWorld(): World
    {
        return new World('full', 'full', [
            $this->createPlayer('id1'),
            $this->createPlayer('id2'),
            $this->createPlayer('id3'),
        ]);
    }

    private function createAvailableWorld(): World
    {
        return new World('available', 'available', [
            $this->createPlayer('id1'),
        ]);
    }

    private function createPlayer(string $id): Player
    {
        return new Player($id, '', new Vector(0, 0));
    }
}
