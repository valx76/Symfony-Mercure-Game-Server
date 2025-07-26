<?php

namespace App\Tests\_Helper\Stub;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Jwt\StaticTokenProvider;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\Update;

final class HubStub implements HubInterface
{
    /** @var Update[] */
    public array $updates;

    public function __construct(
        #[Autowire('%env(MERCURE_URL)%')]
        private readonly string $mercureUrl,

        #[Autowire('%env(MERCURE_PUBLIC_URL)%')]
        private readonly string $mercurePublicUrl,
    ) {
        $this->updates = [];
    }

    public function getUrl(): string
    {
        return $this->mercureUrl;
    }

    public function getPublicUrl(): string
    {
        return $this->mercurePublicUrl;
    }

    public function getProvider(): TokenProviderInterface
    {
        return new StaticTokenProvider('');
    }

    public function getFactory(): ?TokenFactoryInterface
    {
        return null;
    }

    public function publish(Update $update): string
    {
        $this->updates[] = $update;

        return 'id';
    }
}
