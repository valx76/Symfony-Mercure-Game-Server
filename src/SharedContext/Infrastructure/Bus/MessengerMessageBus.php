<?php

namespace App\SharedContext\Infrastructure\Bus;

use App\SharedContext\Application\Bus\AsyncMessageInterface;
use App\SharedContext\Application\Bus\MessageBusInterface;
use App\SharedContext\Application\Bus\SyncMessageInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;

final readonly class MessengerMessageBus implements MessageBusInterface
{
    public function __construct(
        private \Symfony\Component\Messenger\MessageBusInterface $messageBus,
    ) {
    }

    /**
     * @throws ExceptionInterface
     */
    public function execute(AsyncMessageInterface|SyncMessageInterface $message): void
    {
        $this->messageBus->dispatch($message);
    }
}
