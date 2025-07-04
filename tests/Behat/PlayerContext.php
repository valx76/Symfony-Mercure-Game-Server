<?php

namespace App\Tests\Behat;

use App\Game\Domain\Model\Entity\Player;
use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Domain\Service\LevelFactory;
use App\SharedContext\Domain\Model\ValueObject\Vector;
use App\SharedContext\Domain\Service\UuidGeneratorInterface;
use App\Tests\_Helper\RedisHelperTrait;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class PlayerContext implements Context
{
    use RedisHelperTrait;

    private WorldContext $worldContext;

    public private(set) Player $player;

    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        private readonly PlayerRepositoryInterface $playerRepository,
        private readonly LevelFactory $levelFactory,

        #[Autowire('%env(string:WORLD_DEFAULT_LEVEL)%')]
        public private(set) readonly string $defaultLevelName,
    ) {
    }

    #[BeforeScenario]
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        /** @var InitializedSymfonyExtensionEnvironment $environment */
        $environment = $scope->getEnvironment();

        /** @var WorldContext $worldContext */
        $worldContext = $environment->getContext(WorldContext::class);;
        $this->worldContext = $worldContext;
    }

    #[Given('/^My player name is "([^"]*)"$/')]
    public function myPlayerNameIs(string $playerName): void
    {
        $this->iHaveAPlayerIdThatDoesNotExist($playerName);
    }

    #[Given('/^I have a player id that exists$/')]
    public function iHaveAPlayerIdThatExist(): void
    {
        $level = $this->levelFactory->create($this->defaultLevelName);

        $this->player = new Player(
            $this->uuidGenerator->generate(),
            'testPlayer',
            $level->getSpawnPosition(),
            new \DateTimeImmutable(),
            $this->worldContext->world->id,
            $this->defaultLevelName,
        );

        $this->playerRepository->save($this->player);
    }

    #[Given('/^I have a player id that does not exist$/')]
    public function iHaveAPlayerIdThatDoesNotExist(?string $playerName = null): void
    {
        $this->player = new Player(
            $this->uuidGenerator->generate(),
            $playerName ?? 'testPlayer',
            new Vector(0, 0),
            new \DateTimeImmutable()
        );
    }
}
