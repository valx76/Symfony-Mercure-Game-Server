<?php

namespace App\Tests\Unit\Game\Domain\Service;

use App\Game\Domain\Exception\LevelNotFoundException;
use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Level\Level2;
use App\Game\Domain\Service\LevelFactory;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LevelFactoryTest extends TestCase
{
    private LevelFactory $levelFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->levelFactory = new LevelFactory();
    }

    #[DataProvider('levelsProvider')]
    public function testCorrectClassIsInstantiated(string $levelName): void
    {
        $this->assertSame($levelName, $this->levelFactory->create($levelName)::class);
    }

    public function testThrowExceptionWhenLevelNotFound(): void
    {
        $this->expectException(LevelNotFoundException::class);
        $this->levelFactory->create('');
    }

    public static function levelsProvider(): \Generator
    {
        yield [Level1::class];
        yield [Level2::class];
    }
}
