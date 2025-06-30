<?php

namespace App\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Step\Given;

final class MercureContext implements Context
{
    #[Given('/^A notification should be sent to the players on the same world and level$/')]
    public function aNotificationShouldBeSentToThePlayersOnTheSameWorldAndLevel(): void
    {
        // TODO - Create a HubStub like this: https://symfony.com/doc/current/mercure.html#testing
        // -> Store the messages in an array when we publish?
        // --> So we can just check in the array if we have the message we are looking for?
        // -----> Think about what we want to test!

        throw new PendingException();
    }
}
