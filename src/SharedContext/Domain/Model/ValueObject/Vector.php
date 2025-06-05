<?php

namespace App\SharedContext\Domain\Model\ValueObject;

use App\SharedContext\Domain\Exception\InvalidVectorDataException;
use App\SharedContext\Domain\Exception\VectorNegativeValueException;

final readonly class Vector implements \Stringable
{
    /**
     * @throws VectorNegativeValueException
     */
    public function __construct(
        public private(set) int $x,
        public private(set) int $y,
    ) {
        if ($x < 0 || $y < 0) {
            throw new VectorNegativeValueException();
        }
    }

    /**
     * @throws VectorNegativeValueException
     * @throws InvalidVectorDataException
     */
    public static function fromString(string $vector): self
    {
        $parts = explode(',', $vector);

        if (2 !== count($parts)) {
            throw new InvalidVectorDataException();
        }

        return new self((int) $parts[0], (int) $parts[1]);
    }

    public function __toString(): string
    {
        return $this->x.','.$this->y;
    }
}
