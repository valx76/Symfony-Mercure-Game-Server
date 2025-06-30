<?php

namespace App\Tests\_Helper;

use App\Tests\Behat\RedisContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;

abstract class BaseControllerContext implements Context
{
    protected RedisContext $redisContext;

    #[BeforeScenario]
    public function gatherContexts(BeforeScenarioScope $scope): void
    {
        /** @var InitializedSymfonyExtensionEnvironment $environment */
        $environment = $scope->getEnvironment();

        /** @var RedisContext $redisContext */
        $redisContext = $environment->getContext(RedisContext::class);
        $this->redisContext = $redisContext;

        $this->abstractSpecificContext($scope);
    }

    abstract public function abstractSpecificContext(BeforeScenarioScope $scope): void;

    #[AfterScenario]
    public function cleanup(): void
    {
        $this->redisContext->cleanup();
    }
}
