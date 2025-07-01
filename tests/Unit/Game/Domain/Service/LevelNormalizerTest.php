<?php

namespace App\Tests\Unit\Game\Domain\Service;

use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Level\Level2;
use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Service\LevelNormalizer;
use App\Game\Domain\Service\LevelNormalizerInterface;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;

class LevelNormalizerTest extends TestCase
{
    private LevelNormalizerInterface $levelNormalizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->levelNormalizer = new LevelNormalizer();
    }

    public function testNormalizeCorrectlyWithZeroPlayers(): void
    {
        $level = new Level1();

        $world = new World('worldId', 'worldName', []);

        $this->assertSame([
            'level_name' => Level1::class,
            'width' => $level->getSize()->x,
            'height' => $level->getSize()->y,
            'tiles' => $level->getTiles(),
            'players' => [],
        ], $this->levelNormalizer->normalize($world, $level));
    }

    public function testNormalizeCorrectlyWithPlayers(): void
    {
        $level = new Level2();

        $world = new World('worldId', 'worldName', [
            new Player('id1', 'name1', new Vector(1, 1), 'worldId', Level2::class),
            new Player('id2', 'name2', new Vector(2, 2), 'worldId', Level2::class),
            new Player('id3', 'name3', new Vector(7, 4), 'worldId', Level2::class),
        ]);

        $this->assertSame([
            'level_name' => Level2::class,
            'width' => $level->getSize()->x,
            'height' => $level->getSize()->y,
            'tiles' => $level->getTiles(),
            'players' => [
                ['id' => 'id1', 'name' => 'name1', 'x' => 1, 'y' => 1],
                ['id' => 'id2', 'name' => 'name2', 'x' => 2, 'y' => 2],
                ['id' => 'id3', 'name' => 'name3', 'x' => 7, 'y' => 4],
            ],
        ], $this->levelNormalizer->normalize($world, $level));
    }
}
