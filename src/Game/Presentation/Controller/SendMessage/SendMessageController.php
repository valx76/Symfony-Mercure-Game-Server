<?php

namespace App\Game\Presentation\Controller\SendMessage;

use App\Game\Application\UseCase\SendMessage\SendMessageAsyncMessage;
use App\SharedContext\Application\Bus\MessageBusInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\ObjectMapper\ObjectMapperInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/message', name: 'send_message', methods: ['POST'])]
final class SendMessageController extends AbstractController
{
    public function __construct(
        private readonly MessageBusInterface $bus,
        private readonly ObjectMapperInterface $mapper,
    ) {
    }

    public function __invoke(
        #[MapRequestPayload] SendMessageDTO $dto,
    ): JsonResponse {
        $message = $this->mapper->map($dto, SendMessageAsyncMessage::class);
        $this->bus->execute($message);

        return $this->json([]);
    }
}
