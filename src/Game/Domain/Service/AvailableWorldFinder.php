<?php

namespace App\Game\Domain\Service;

use App\Game\Domain\Exception\NoWorldAvailableException;
use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;

final readonly class AvailableWorldFinder
{
    public function __construct(
        private WorldRepositoryInterface $worldRepository,
        private int $maxPlayersPerWorld,
    ) {
    }

    /**
     * @throws NoWorldAvailableException
     */
    public function find(): World
    {
        $worlds = $this->worldRepository->findAll();

        foreach ($worlds as $world) {
            if ($this->maxPlayersPerWorld <= $world->getPlayersCount()) {
                continue;
            }

            return $world;
        }

        throw new NoWorldAvailableException();
    }
}
