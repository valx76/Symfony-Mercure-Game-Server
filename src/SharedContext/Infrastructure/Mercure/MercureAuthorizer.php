<?php

namespace App\SharedContext\Infrastructure\Mercure;

use App\Game\Domain\Model\Entity\Level\LevelInterface;
use App\SharedContext\Application\Mercure\MercureAuthorizerInterface;
use App\SharedContext\Domain\Model\MercureTopics;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mercure\Authorization;
use Symfony\Component\Mercure\Discovery;

final readonly class MercureAuthorizer implements MercureAuthorizerInterface
{
    /**
     * @param LevelInterface[] $levels
     */
    public function __construct(
        private RequestStack $requestStack,
        private Discovery $discovery,
        private Authorization $authorization,

        #[AutowireIterator('app.level')]
        private iterable $levels,
    ) {
    }

    public function authorize(string $playerId, string $worldId): void
    {
        /** @var Request $request */
        $request = $this->requestStack->getCurrentRequest();

        $this->discovery->addLink($request);
        $this->authorization->setCookie(
            $request,
            $this->generateMercureTopics($playerId, $worldId)
        );
    }

    /**
     * @return string[]
     */
    private function generateMercureTopics(string $playerId, string $worldId): array
    {
        $topics = [
            sprintf(MercureTopics::PLAYER, $playerId),
        ];

        foreach ($this->levels as $level) {
            $topics[] = sprintf(MercureTopics::LEVEL, $worldId, $level::class);
            $topics[] = sprintf(MercureTopics::MESSAGE, $worldId, $level::class);
        }

        return $topics;
    }
}
