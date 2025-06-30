<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Then;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

final class WebContext implements Context
{
    public private(set) Response $response;

    public function __construct(
        private readonly KernelBrowser $client,
    ) {
    }

    #[Then('/^I should have a (\d+) response status code$/')]
    public function iShouldHaveAResponseStatusCode(int $statusCode): void
    {
        Assert::assertSame($statusCode, $this->response->getStatusCode());
    }

    /**
     * @param array<string, mixed> $parameters
     */
    public function request(string $method, string $uri, array $parameters = []): void
    {
        $this->client->jsonRequest($method, $uri, $parameters);
        $this->response = $this->client->getResponse();
    }
}
