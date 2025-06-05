<?php

namespace App\Game\Domain\Exception;

class EntityHasMissingDataException extends \Exception
{
    public static function fromField(string $className, string $field): self
    {
        return new self(
            sprintf('%s has missing data: "%s"', $className, $field)
        );
    }
}
