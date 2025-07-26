<?php

namespace App\Tests\Behat;

use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\SharedContext\Domain\Service\UuidGeneratorInterface;
use App\Tests\_Helper\RedisHelperTrait;
use Behat\Behat\Context\Context;
use Behat\Step\Given;

final class WorldContext implements Context
{
    use RedisHelperTrait;

    public private(set) World $world;

    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly WorldRepositoryInterface $worldRepository,
    ) {
    }

    #[Given('/^An available world exists$/')]
    public function anAvailableWorldExists(): void
    {
        $this->world = new World(
            $this->uuidGenerator->generate(),
            'testWorld',
            [],
        );

        $this->worldRepository->save($this->world);
    }

    #[Given('/^An available world exists with id "([^"]*)"$/')]
    public function anAvailableWorldExistsWithId(string $worldId): void
    {
        $this->world = new World(
            $worldId,
            'testWorld',
            [],
        );

        $this->worldRepository->save($this->world);
    }
}
