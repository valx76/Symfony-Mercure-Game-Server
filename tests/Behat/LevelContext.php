<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\BeforeScenario;
use Behat\Step\Then;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;
use PHPUnit\Framework\Assert;

final class LevelContext implements Context
{
    private PlayerContext $playerContext;

    #[BeforeScenario]
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        /** @var InitializedSymfonyExtensionEnvironment $environment */
        $environment = $scope->getEnvironment();

        /** @var PlayerContext $playerContext */
        $playerContext = $environment->getContext(PlayerContext::class);
        $this->playerContext = $playerContext;
    }

    #[Then('/^I should be on level "([^"]*)"$/')]
    public function iShouldBeOnLevel(string $levelName): void
    {
        Assert::assertSame($levelName, $this->playerContext->player->levelName);
    }
}
