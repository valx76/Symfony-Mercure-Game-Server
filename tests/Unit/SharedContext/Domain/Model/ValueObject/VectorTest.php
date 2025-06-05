<?php

namespace App\Tests\Unit\SharedContext\Domain\Model\ValueObject;

use App\SharedContext\Domain\Exception\InvalidVectorDataException;
use App\SharedContext\Domain\Exception\VectorNegativeValueException;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use PHPUnit\Framework\TestCase;

class VectorTest extends TestCase
{
    public function testXShouldBeZeroOrHigher(): void
    {
        $this->expectException(VectorNegativeValueException::class);
        new Vector(-10, 0);
    }

    public function testYShouldBeZeroOrHigher(): void
    {
        $this->expectException(VectorNegativeValueException::class);
        new Vector(0, -10);
    }

    public function testFromStringShouldHaveAValidFormat(): void
    {
        $this->expectException(InvalidVectorDataException::class);
        Vector::fromString('invalid');
    }

    public function testFromStringIsCreatedCorreclty(): void
    {
        $vectorFromString = Vector::fromString('1,2');
        $vectorFromParams = new Vector(1, 2);

        $this->assertSame($vectorFromParams->x, $vectorFromString->x);
        $this->assertSame($vectorFromParams->y, $vectorFromString->y);
    }

    public function testToString(): void
    {
        $vector = new Vector(1, 2);
        $this->assertSame('1,2', (string) $vector);
    }
}
