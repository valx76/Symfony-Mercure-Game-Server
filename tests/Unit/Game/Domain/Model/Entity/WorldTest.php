<?php

namespace App\Tests\Unit\Game\Domain\Model\Entity;

use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;

class WorldTest extends TestCase
{
    public function testPlayersAreCorrectlyAdded(): void
    {
        $player1 = $this->createPlayer('player1');
        $player2 = $this->createPlayer('player2');

        $world = $this->createWorld();
        $world->addPlayer($player1);
        $world->addPlayer($player2);

        $this->assertTrue($world->hasPlayer($player1));
        $this->assertSame($player1->id, $world->getPlayers()[0]->id);

        $this->assertTrue($world->hasPlayer($player2));
        $this->assertSame($player2->id, $world->getPlayers()[1]->id);
    }

    public function testPlayersAreCorrectlyRemoved(): void
    {
        $player1 = $this->createPlayer('player1');
        $player2 = $this->createPlayer('player2');

        $world = $this->createWorld();
        $world->addPlayer($player1);
        $world->addPlayer($player2);

        $this->assertTrue($world->hasPlayer($player1));
        $world->removePlayer($player1);
        $this->assertFalse($world->hasPlayer($player1));

        $this->assertTrue($world->hasPlayer($player2));
        $world->removePlayer($player2);
        $this->assertFalse($world->hasPlayer($player2));
    }

    public function testPlayersCountIsCorrect(): void
    {
        $world = $this->createWorld();

        $this->assertSame(0, $world->getPlayersCount());

        $player1 = $this->createPlayer('player1');

        $world->addPlayer($player1);
        $this->assertSame(1, $world->getPlayersCount());

        $world->addPlayer($this->createPlayer('player2'));
        $this->assertSame(2, $world->getPlayersCount());

        $world->removePlayer($player1);
        $this->assertSame(1, $world->getPlayersCount());

        $world->removePlayer($this->createPlayer('player3'));
        $this->assertSame(1, $world->getPlayersCount());
    }

    private function createWorld(): World
    {
        return new World('world', '', []);
    }

    private function createPlayer(string $id): Player
    {
        return new Player($id, '', new Vector(0, 0), null, null);
    }
}
