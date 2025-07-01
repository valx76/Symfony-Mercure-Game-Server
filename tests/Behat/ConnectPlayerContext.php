<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Tests\_Helper\BaseControllerContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Step\Then;
use Behat\Step\When;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;
use PHPUnit\Framework\Assert;

final class ConnectPlayerContext extends BaseControllerContext
{
    private WebContext $webContext;
    private PlayerContext $playerContext;
    private WorldContext $worldContext;

    public function abstractSpecificContext(BeforeScenarioScope $scope): void
    {
        /** @var InitializedSymfonyExtensionEnvironment $environment */
        $environment = $scope->getEnvironment();

        /** @var WebContext $webContext */
        $webContext = $environment->getContext(WebContext::class);
        $this->webContext = $webContext;

        /** @var PlayerContext $playerContext */
        $playerContext = $environment->getContext(PlayerContext::class);
        $this->playerContext = $playerContext;

        /** @var WorldContext $worldContext */
        $worldContext = $environment->getContext(WorldContext::class);
        $this->worldContext = $worldContext;
    }

    #[When('/^I connect$/')]
    public function iConnect(): void
    {
        $this->webContext->request('POST', '/connect', [
            'playerName' => $this->playerContext->player->name,
        ]);
    }

    #[Then('/^I should have a correct ConnectPlayer response$/')]
    public function iShouldHaveACorrectConnectPlayerResponse(): void
    {
        /** @var string $response */
        $response = $this->webContext->response->getContent();

        $result = json_decode($response, true);

        /* @phpstan-ignore-next-line */
        Assert::assertArrayHasKey('playerId', $result);

        /* @phpstan-ignore-next-line */
        Assert::assertCount(1, $result['levelData']['players']);

        /* @phpstan-ignore-next-line */
        Assert::assertSame($result['playerId'], $result['levelData']['players'][0]['id']);

        /* @phpstan-ignore-next-line */
        Assert::assertSame($this->worldContext->world->id, $result['worldId']);

        /* @phpstan-ignore-next-line */
        Assert::assertSame($this->playerContext->defaultLevelName, $result['levelData']['level_name']);
    }
}
