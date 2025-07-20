<?php

namespace App\Tests\Unit\Game\Infrastructure\Validator;

use App\Game\Domain\Model\Repository\PlayerRepositoryInterface;
use App\Game\Infrastructure\Validator\PlayerExists;
use App\Game\Infrastructure\Validator\PlayerExistsValidator;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Test\ConstraintValidatorTestCase;

class PlayerExistsValidatorTest extends ConstraintValidatorTestCase
{
    final public string $EXISTING_PLAYER_ID = 'existing-player';
    final public string $NON_EXISTING_PLAYER_ID = 'non-existing-player';

    protected function createValidator(): ConstraintValidatorInterface
    {
        $playerRepository = $this->createMock(PlayerRepositoryInterface::class);
        $playerRepository
            ->method('exists')->willReturnCallback(fn (string $playerId) => match ($playerId) {
                $this->EXISTING_PLAYER_ID => true,
                $this->NON_EXISTING_PLAYER_ID => false,
                default => throw new \Exception(),
            });

        return new PlayerExistsValidator($playerRepository);
    }

    public function testPlayerExists(): void
    {
        $constraint = new PlayerExists();
        $this->validator->validate($this->EXISTING_PLAYER_ID, $constraint);
        $this->assertNoViolation();
    }

    public function testPlayerNotExists(): void
    {
        $constraint = new PlayerExists();
        $this->validator->validate($this->NON_EXISTING_PLAYER_ID, $constraint);
        $this->buildViolation($constraint->message)
            ->setParameter('{{ id }}', $this->NON_EXISTING_PLAYER_ID)
            ->assertRaised();
    }
}
