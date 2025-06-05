<?php

namespace App\SharedContext\Domain\Exception;

final class DatabaseFieldNotFoundException extends \Exception
{
    public function __construct(
        public private(set) readonly string $name,
    ) {
        parent::__construct();
    }
}
