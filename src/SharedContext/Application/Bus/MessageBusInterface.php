<?php

namespace App\SharedContext\Application\Bus;

interface MessageBusInterface
{
    public function execute(MessageInterface $message): void;
}
