<?php

namespace App\SharedContext\Application\Bus;

interface MessageBusInterface
{
    public function execute(AsyncPlayerMessageInterface|SyncMessageInterface $message): void;
}
