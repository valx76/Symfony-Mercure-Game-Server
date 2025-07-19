<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Tests\_Helper\BaseControllerContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Step\Then;
use Behat\Step\When;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;
use PHPUnit\Framework\Assert;

final class SendMessageContext extends BaseControllerContext
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

    #[When('/^I send the message "([^"]*)"$/')]
    public function iSendTheMessage(string $message): void
    {
        $this->webContext->request('POST', '/message', [
            'playerId' => $this->playerContext->player->id ?? '',
            'message' => $message,
        ]);
    }

    #[Then('/^I should have a correct SendMessage response$/')]
    public function iShouldHaveACorrectSendMessageResponse(): void
    {
        /** @var string $response */
        $response = $this->webContext->response->getContent();

        $result = json_decode($response, true);

        /* @phpstan-ignore-next-line */
        Assert::assertCount(0, $result);
    }
}
