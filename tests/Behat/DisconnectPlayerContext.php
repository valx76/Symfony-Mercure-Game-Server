<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Tests\_Helper\BaseControllerContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Step\Then;
use Behat\Step\When;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;
use PHPUnit\Framework\Assert;

final class DisconnectPlayerContext extends BaseControllerContext
{
    private WebContext $webContext;
    private PlayerContext $playerContext;

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
    }

    #[When('/^I disconnect$/')]
    public function iDisconnect(): void
    {
        $this->webContext->request('POST', '/disconnect', [
            'playerId' => $this->playerContext->player->id ?? '',
        ]);
    }

    #[Then('/^I should have a correct DisconnectPlayer response$/')]
    public function iShouldHaveACorrectDisconnectPlayerResponse(): void
    {
        /** @var string $response */
        $response = $this->webContext->response->getContent();

        $result = json_decode($response, true);

        /* @phpstan-ignore-next-line */
        Assert::assertCount(0, $result);
    }
}
