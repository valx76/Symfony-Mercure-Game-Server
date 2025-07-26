<?php

namespace App\Tests\Behat;

use App\Tests\_Helper\Stub\HubStub;
use Behat\Behat\Context\Context;
use Behat\Step\Then;
use PHPUnit\Framework\Assert;
use Symfony\Component\Mercure\Update;

final class MercureContext implements Context
{
    public function __construct(
        private readonly HubStub $hub,
    ) {
    }

    #[Then('/^I should have a Mercure update with topic "([^"]*)" and content$/')]
    public function iShouldHaveAMercureUpdateWithTopicAndContent(string $topicName, string $content): void
    {
        Assert::assertTrue(
            array_any(
                $this->hub->updates,
                fn (Update $update) => $update->getTopics()[0] === $topicName && $update->getData() === $content
            )
        );
    }
}
