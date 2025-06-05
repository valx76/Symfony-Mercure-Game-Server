<?php

namespace App\Game\Presentation\Controller\MovePlayer;

use App\Game\Application\UseCase\MovePlayer\MovePlayerAsyncMessage;
use App\SharedContext\Application\Bus\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/move', name: 'move_player', methods: ['POST'])]
final class MovePlayerController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] MovePlayerDTO $dto,
    ): JsonResponse {
        $this->bus->execute(new MovePlayerAsyncMessage($dto->playerId, $dto->targetX, $dto->targetY));

        return $this->json([]);
    }
}
