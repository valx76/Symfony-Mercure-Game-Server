<?php

namespace App\Game\Presentation\Controller\ConnectPlayer;

use App\Game\Application\UseCase\ConnectPlayer\ConnectPlayerSyncMessage;
use App\SharedContext\Domain\Service\UuidGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/connect', name: 'connect_player', methods: ['POST'])]
final class ConnectPlayerController extends AbstractController
{
    use HandleTrait;

    public function __construct(
        private readonly UuidGeneratorInterface $uuidGenerator,
        /** @phpstan-ignore property.onlyWritten */
        private MessageBusInterface $messageBus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] ConnectPlayerDTO $dto,
    ): JsonResponse {
        $playerId = $this->uuidGenerator->generate();

        $result = $this->handle(
            new ConnectPlayerSyncMessage($playerId, $dto->playerName)
        );

        return $this->json($result);
    }
}
