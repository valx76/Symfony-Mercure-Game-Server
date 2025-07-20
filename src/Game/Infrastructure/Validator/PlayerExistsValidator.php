<?php

namespace App\Game\Infrastructure\Validator;

use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class PlayerExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly PlayerRepositoryInterface $playerRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof PlayerExists) {
            throw new UnexpectedTypeException($constraint, PlayerExists::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }

        if (!$this->playerRepository->exists($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ id }}', $value)
                ->addViolation();
        }
    }
}
