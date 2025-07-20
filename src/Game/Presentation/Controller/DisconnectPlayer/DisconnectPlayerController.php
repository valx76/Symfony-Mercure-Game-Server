<?php

namespace App\Game\Presentation\Controller\DisconnectPlayer;

use App\Game\Application\UseCase\DisconnectPlayer\DisconnectPlayerAsyncMessage;
use App\SharedContext\Application\Bus\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/disconnect', name: 'disconnect_player', methods: ['POST'])]
final class DisconnectPlayerController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ObjectMapperInterface $mapper,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] DisconnectPlayerDTO $dto,
    ): JsonResponse {
        $message = $this->mapper->map($dto, DisconnectPlayerAsyncMessage::class);
        $this->bus->execute($message);

        return $this->json([]);
    }
}
