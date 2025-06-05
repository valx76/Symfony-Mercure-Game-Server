<?php

namespace App\Tests\Unit\SharedContext\Infrastructure\Uuid;

use App\SharedContext\Infrastructure\Uuid\SymfonyUuidGenerator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;

class SymfonyUuidGeneratorTest extends TestCase
{
    private SymfonyUuidGenerator $uuidGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->uuidGenerator = new SymfonyUuidGenerator();
    }

    public function testGeneratedUuidIsCorrect(): void
    {
        $uuid = $this->uuidGenerator->generate();

        $this->assertTrue(
            Uuid::isValid($uuid)
        );
    }
}
