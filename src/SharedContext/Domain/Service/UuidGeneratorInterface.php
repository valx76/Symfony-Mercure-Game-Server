<?php

namespace App\SharedContext\Domain\Service;

interface UuidGeneratorInterface
{
    public function generate(): string;
}
