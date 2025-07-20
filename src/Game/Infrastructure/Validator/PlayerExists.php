<?php

namespace App\Game\Infrastructure\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
final class PlayerExists extends Constraint
{
    public function __construct(
        public readonly string $message = 'Player with id "{{ id }}" not found!',
        ?array $groups = null,
        mixed $payload = null,
    ) {
        parent::__construct([], $groups, $payload);
    }
}
