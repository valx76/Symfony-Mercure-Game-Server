<?php

namespace App\Tests\Unit\Game\Infrastructure\Mercure;

use App\Game\Domain\Model\Entity\Level\Level1;
use App\Game\Domain\Model\Entity\Level\Level2;
use App\Game\Domain\Model\MercureTopics;
use App\Game\Infrastructure\Mercure\MercureAuthorizer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;
use Symfony\Component\Mercure\HubRegistry;
use Symfony\Component\Mercure\Jwt\TokenFactoryInterface;
use Symfony\Component\Mercure\Jwt\TokenProviderInterface;
use Symfony\Component\Mercure\MockHub;

class MercureAuthorizerTest extends TestCase
{
    public function testAuthorize(): void
    {
        $worldId = 'worldId';
        $playerId = 'playerId';
        $request = new Request();

        $requestStack = $this->createMock(RequestStack::class);
        $requestStack->method('getCurrentRequest')->willReturn($request);

        $tokenProvider = $this->createMock(TokenProviderInterface::class);
        $tokenFactory = $this->createMock(TokenFactoryInterface::class);

        // Used to check what has been sent to `$authorization->authorize()`
        $tokenFactory->method('create')->willReturnCallback(function (array $topics) use ($worldId, $playerId): string {
            sort($topics);

            $this->assertSame(sprintf(MercureTopics::LEVEL, $worldId, Level1::class), $topics[0]);
            $this->assertSame(sprintf(MercureTopics::LEVEL, $worldId, Level2::class), $topics[1]);
            $this->assertSame(sprintf(MercureTopics::MESSAGE, $worldId, Level1::class), $topics[2]);
            $this->assertSame(sprintf(MercureTopics::MESSAGE, $worldId, Level2::class), $topics[3]);
            $this->assertSame(sprintf(MercureTopics::PLAYER, $playerId), $topics[4]);

            return 'id';
        });

        $hub = new MockHub('', $tokenProvider, fn (): string => 'id', $tokenFactory);
        $hubRegistry = new HubRegistry($hub);

        $discovery = new Discovery($hubRegistry);

        $authorization = new Authorization($hubRegistry);

        $mercureAuthorizer = new MercureAuthorizer($requestStack, $discovery, $authorization, [new Level1(), new Level2()]);
        $mercureAuthorizer->authorize($playerId, $worldId);

        // Used to check that `$discovery->addLink()` has been called
        $this->assertTrue($request->attributes->has('_links'));
    }
}
