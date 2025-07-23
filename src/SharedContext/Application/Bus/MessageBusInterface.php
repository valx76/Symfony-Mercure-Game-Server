<?php

namespace App\SharedContext\Application\Bus;

interface MessageBusInterface
{
    public function execute(BaseWithPlayerMessageInterface|AsyncPendingMessageInterface|SyncMessageInterface $message): void;
}
