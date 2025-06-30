<?php

namespace App\Tests\Behat;

use App\Game\Domain\Model\Entity\World;
use App\Game\Domain\Model\Repository\WorldRepositoryInterface;
use App\SharedContext\Domain\Service\UuidGeneratorInterface;
use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

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
}
