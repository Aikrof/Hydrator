<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Tests\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests\Hydrator;

use Aikrof\Hydrator\Exceptions\HydratorExeption;
use Aikrof\Hydrator\Hydrator;
use Aikrof\Hydrator\Tests\Fixtures\Entities\EmptyEntity;
use Aikrof\Hydrator\Tests\Fixtures\Entities\UserEntity;
use Aikrof\Hydrator\Tests\TestCase;

/**
 * Class HydrateTest
 *
 * @group Hydrator
 */
class HydrateTest extends TestCase
{
    /**
     * Hydrate data from array to object
     */
    public function testHydrateDataToObject(): void
    {
        /** @var \Aikrof\Hydrator\Tests\Fixtures\Entities\UserEntity $entity */
        $entity = Hydrator::hydrate(new UserEntity(), $this->userData);

        $this->checkValidHydrate($entity);
    }

    /**
     * Create object from string and hydrate data from array to created object
     */
    public function testCreateObjectFromStringAndHydrateData(): void
    {
        $entity = Hydrator::hydrate(UserEntity::class, $this->userData);

        $this->checkValidHydrate($entity);
    }

    /**
     * Check valid entity data
     *
     * @param object $entity
     */
    private function checkValidHydrate(object $entity): void
    {
        // check fields that need to be ignored
        $this->assertNull($entity->getId());
        $this->assertNull($entity->getPaymentMethod());

        // user data check
        $this->assertSame($this->userData['uid'], $entity->getUid());
        $this->assertSame($this->userData['login'], $entity->getLogin());
        $this->assertSame($this->userData['password'], $entity->getPassword());
        $this->assertSame($this->userData['firstName'], $entity->getFirstName());
        $this->assertSame($this->userData['lastName'], $entity->getLastName());
        $this->assertSame($this->userData['email'], $entity->getEmail());

        // user.birthday data check
        /** @var \Aikrof\Hydrator\Tests\Fixtures\Entities\BirthdayEntity $birthday */
        $birthday = $entity->getBirthday();
        $this->assertSame($this->userBirthday['uid'], $birthday->getUid());
        $this->assertSame($this->userBirthday['day'], $birthday->getDay());
        $this->assertSame($this->userBirthday['month'], $birthday->getMonth());
        $this->assertSame($this->userBirthday['year'], $birthday->getYear());

        // user.address data check
        /** @var \Aikrof\Hydrator\Tests\Fixtures\Entities\AddressEntity $address */
        $address = \current($entity->getAddress());
        $this->assertSame($this->userAddress['uid'], $address->getUid());
        $this->assertSame($this->userAddress['city'], $address->getCity());
        $this->assertSame($this->userAddress['street'], $address->getStreet());
        $this->assertSame($this->userAddress['houseNumber'], $address->getHouseNumber());
        $this->assertSame($this->userAddress['phone'], $address->getPhone());
    }

    /**
     * @dataProvider invalidTypeDataProvider
     *
     * Check types, if entity annotation type not equal array field type, then field need to be default or null.
     *
     * @param array $data
     */
    public function testInvalidTypes(array $data): void
    {
        $entity = Hydrator::hydrate(UserEntity::class, $data);

        // fields that have default values
        $this->assertStringEndsWith('0000-0000-0000-0000', $entity->getUid());
        $this->assertStringEndsWith('default', $entity->getLogin());
        $this->assertStringEndsWith('default@email.com', $entity->getEmail());

        // fields that have to be null
        $this->assertNull($entity->getFirstName());
        $this->assertNull($entity->getLastName());
    }

    /**
     * @return array
     */
    public function invalidTypeDataProvider(): array
    {
        return [
            [[
                'uid' => true,
                'login' => 1,
                'firstName' => false,
                'lastName' => 33.2,
                'email' => new UserEntity(),
            ]]
        ];
    }

    /**
     * Empty object test
     */
    public function testException(): void
    {
        $this->expectException(HydratorExeption::class);
        $this->expectExceptionMessage('Cannot get properties from: `Aikrof\Hydrator\Tests\Fixtures\Entities\EmptyEntity`');
        Hydrator::hydrate(EmptyEntity::class, []);
    }
}