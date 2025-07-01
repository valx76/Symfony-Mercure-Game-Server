<?php

namespace App\Game\Domain\Service;

use App\Game\Domain\Model\Entity\Level\LevelInterface;
use App\Game\Domain\Model\Entity\World;

interface LevelNormalizerInterface
{
    /** @return array<string, int|string|array<int|array<string, int|string>>> */
    public function normalize(World $world, LevelInterface $level): array;
}
