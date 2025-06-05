<?php

namespace App\Tests\Unit\SharedContext\Domain\Service;

use App\SharedContext\Domain\Exception\PositionOutOfAreaException;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use App\SharedContext\Domain\Service\VectorUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class VectorUtilsTest extends TestCase
{
    #[DataProvider('vectorInVectorProvider')]
    public function testVectorInVector(Vector $inner, Vector $outer, bool $expected): void
    {
        $this->assertSame($expected, VectorUtils::isVectorInVector($inner, $outer));
    }

    /** @param int[] $tiles */
    #[DataProvider('positionCollidingProvider')]
    public function testPositionColliding(Vector $position, Vector $size, array $tiles, bool $expected): void
    {
        $this->assertSame($expected, VectorUtils::isPositionColliding($position, $size, $tiles));
    }

    public function testFailureWhenPositionOutsideOfArea(): void
    {
        $this->expectException(PositionOutOfAreaException::class);
        VectorUtils::isPositionColliding(new Vector(2, 2), new Vector(1, 1), [0, 1]);
    }

    public static function vectorInVectorProvider(): \Generator
    {
        yield [new Vector(0, 0), new Vector(0, 0), true];
        yield [new Vector(0, 1), new Vector(0, 1), true];
        yield [new Vector(1, 0), new Vector(1, 0), true];
        yield [new Vector(5, 2), new Vector(10, 2), true];
        yield [new Vector(2, 5), new Vector(2, 10), true];
        yield [new Vector(1, 0), new Vector(0, 2), false];
        yield [new Vector(0, 1), new Vector(2, 0), false];
        yield [new Vector(5, 1), new Vector(4, 1), false];
        yield [new Vector(1, 5), new Vector(1, 4), false];
    }

    public static function positionCollidingProvider(): \Generator
    {
        yield [new Vector(0, 0), new Vector(0, 0), [], false];

        yield [new Vector(0, 0), new Vector(1, 1), [0], false];
        yield [new Vector(0, 0), new Vector(1, 1), [1], true];

        yield [new Vector(0, 0), new Vector(2, 2), [0, 1, 1, 0], false];
        yield [new Vector(1, 0), new Vector(2, 2), [0, 1, 1, 0], true];
        yield [new Vector(0, 1), new Vector(2, 2), [0, 1, 1, 0], true];
        yield [new Vector(1, 1), new Vector(2, 2), [0, 1, 1, 0], false];
    }
}
