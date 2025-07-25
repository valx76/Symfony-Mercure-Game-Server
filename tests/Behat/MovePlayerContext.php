<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\SharedContext\Domain\Model\ValueObject\Vector;
use App\Tests\_Helper\BaseControllerContext;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Step\Then;
use Behat\Step\When;
use FriendsOfBehat\SymfonyExtension\Context\Environment\InitializedSymfonyExtensionEnvironment;
use PHPUnit\Framework\Assert;

final class MovePlayerContext extends BaseControllerContext
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

    #[When('/^I move to position "([^"]*)"$/')]
    public function iMoveToPosition(string $position): void
    {
        $vector = Vector::fromString($position);

        $this->webContext->request('POST', '/move', [
            'playerId' => $this->playerContext->player->id ?? '',
            'targetX' => $vector->x,
            'targetY' => $vector->y,
        ]);

        $this->playerContext->refreshPlayer();
    }

    #[Then('/^I should have a correct MovePlayer response$/')]
    public function iShouldHaveACorrectMovePlayerResponse(): void
    {
        /** @var string $response */
        $response = $this->webContext->response->getContent();

        $result = json_decode($response, true);

        /* @phpstan-ignore-next-line */
        Assert::assertCount(0, $result);
    }
}
