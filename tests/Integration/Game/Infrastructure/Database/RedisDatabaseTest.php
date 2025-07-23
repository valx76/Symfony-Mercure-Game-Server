<?php

namespace App\Tests\Integration\Game\Infrastructure\Database;

use App\SharedContext\Domain\Exception\DatabaseFieldNotFoundException;
use App\SharedContext\Domain\Exception\DatabaseKeyNotFoundException;
use App\SharedContext\Infrastructure\Database\RedisDatabase;
use App\Tests\_Helper\RedisHelperTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RedisDatabaseTest extends KernelTestCase
{
    use RedisHelperTrait;

    private RedisDatabase $redisDatabase;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = self::getContainer();

        /** @var RedisDatabase $db */
        $db = $container->get(RedisDatabase::class);
        $this->redisDatabase = $db;
    }

    protected function tearDown(): void
    {
        $this->deleteTestKeys($this->redisDatabase);
    }

    public function testFailureToRetrieveNonExistingKey(): void
    {
        $this->expectException(DatabaseKeyNotFoundException::class);
        $this->redisDatabase->getValue('non-existing-key');
    }

    public function testFailureToDeleteNonExistingKey(): void
    {
        $this->expectException(DatabaseKeyNotFoundException::class);
        $this->redisDatabase->deleteKey('non-existing-key');
    }

    public function testDeleteExistingKey(): void
    {
        $key = 'key';

        $this->redisDatabase->setValue($key, '');
        $this->redisDatabase->deleteKey($key);

        $this->assertFalse($this->redisDatabase->hasKey($key));
    }

    public function testFailureToDeleteNonExistingHashKey(): void
    {
        $this->expectException(DatabaseKeyNotFoundException::class);
        $this->redisDatabase->deleteHashField('non-existing-key', 'non-existing-field');
    }

    public function testFailureToDeleteNonExistingHashField(): void
    {
        $key = 'key';

        $this->redisDatabase->setHashValue($key, 'field', '');

        $this->expectException(DatabaseFieldNotFoundException::class);
        $this->redisDatabase->deleteHashField($key, 'non-existing-field');
    }

    public function testDeleteExistingHashField(): void
    {
        $key = 'key';
        $field = 'field';

        $this->redisDatabase->setHashValue($key, $field, '');
        $this->redisDatabase->deleteHashField($key, $field);

        $this->assertFalse($this->redisDatabase->hasHashField($key, $field));
    }

    public function testCheckIfKeyExists(): void
    {
        $key = 'key';

        $this->assertFalse($this->redisDatabase->hasKey($key));
        $this->redisDatabase->setValue($key, '');
        $this->assertTrue($this->redisDatabase->hasKey($key));
        $this->redisDatabase->deleteKey($key);
        $this->assertFalse($this->redisDatabase->hasKey($key));
    }

    public function testGetSetKey(): void
    {
        $key = 'key';
        $value = 'value';

        $this->assertFalse($this->redisDatabase->hasKey($key));
        $this->redisDatabase->setValue($key, $value);
        $this->assertTrue($this->redisDatabase->hasKey($key));
        $this->assertSame($value, $this->redisDatabase->getValue($key));
    }

    public function testCheckIfHashFieldExists(): void
    {
        $key = 'key';
        $field = 'field';

        $this->assertFalse($this->redisDatabase->hasHashField($key, $field));
        $this->redisDatabase->setHashValue($key, $field, '');
        $this->assertTrue($this->redisDatabase->hasHashField($key, $field));
        $this->redisDatabase->deleteHashField($key, $field);
        $this->assertFalse($this->redisDatabase->hasHashField($key, $field));
    }

    public function testFailureToRetrieveNonExistingHashField(): void
    {
        $key = 'key';

        $this->redisDatabase->setHashValue($key, 'field', '');

        $this->expectException(DatabaseFieldNotFoundException::class);
        $this->redisDatabase->getHashValue($key, 'non-existing-field');
    }

    public function testGetSetHashField(): void
    {
        $key = 'key';
        $field = 'field';
        $value = 'value';

        $this->assertFalse($this->redisDatabase->hasHashField($key, $field));
        $this->redisDatabase->setHashValue($key, $field, $value);
        $this->assertTrue($this->redisDatabase->hasKey($key));
        $this->assertSame($value, $this->redisDatabase->getHashValue($key, $field));
    }

    public function testPushPopValueOfSet(): void
    {
        $key = 'key';

        $this->assertNull($this->redisDatabase->popValueFromSet($key));

        $this->redisDatabase->pushValueToSet($key, 'value1');
        $this->redisDatabase->pushValueToSet($key, 'value2');

        $this->assertSame('value2', $this->redisDatabase->popValueFromSet($key));
        $this->assertSame('value1', $this->redisDatabase->popValueFromSet($key));
        $this->assertNull($this->redisDatabase->popValueFromSet($key));
    }

    public function testFindKeysByPattern(): void
    {
        $this->redisDatabase->setValue('key1', '');
        $this->redisDatabase->setValue('key2', '');
        $this->redisDatabase->setValue('another-key', '');
        $this->redisDatabase->setHashValue('hash', 'field', '');

        $this->assertCount(0, $this->redisDatabase->findKeysByPattern('non-existing-key'));
        $this->assertCount(2, $this->redisDatabase->findKeysByPattern('k*'));
        $this->assertCount(1, $this->redisDatabase->findKeysByPattern('k*2'));
        $this->assertCount(4, $this->redisDatabase->findKeysByPattern('*'));
    }
}
