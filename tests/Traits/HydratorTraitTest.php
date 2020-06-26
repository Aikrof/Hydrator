<?php
/**
 * @link https://github.com/Aikrof
 * @package @package Aikrof\Hydrator\Tests\Traits
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests\Traits;

use Aikrof\Hydrator\Tests\Fixtures\Entities\UserEntity;
use Aikrof\Hydrator\Tests\TestCase;

/**
 * Class HydratorTraitTest
 *
 * @group Hydrator
 */
class HydratorTraitTest extends TestCase
{
    /**
     * Extract data from object to array.
     */
    public function testExtractFromObject(): void
    {
        /** @var UserEntity $user */
        $user = $this->fillUserEntity(new UserEntity());

        $data = $user->extractData();

        // Check ignored fields
        $this->assertArrayNotHasKey('id', $data);
        $this->assertArrayNotHasKey('paymentMethod', $data);

        $this->checkValid($user, $data);
    }

    /**
     * Hydrate data from array to object.
     */
    public function testHydrateToObject(): void
    {
        $user = new UserEntity();

        $user->hydrateData($this->userData);

        // check fields that need to be ignored
        $this->assertNull($user->getId());
        $this->assertNull($user->getPaymentMethod());

        $this->checkValid($user, $this->userData);
    }

    private function checkValid(UserEntity $entity, array $data): void
    {
        // user data check
        $this->assertSame($data['uid'], $entity->getUid());
        $this->assertSame($data['login'], $entity->getLogin());
        $this->assertSame($data['password'], $entity->getPassword());
        $this->assertSame($data['firstName'], $entity->getFirstName());
        $this->assertSame($data['lastName'], $entity->getLastName());
        $this->assertSame($data['email'], $entity->getEmail());

        // user.birthday data check
        /** @var \Aikrof\Hydrator\Tests\Fixtures\Entities\BirthdayEntity $birthday */
        $birthday = $entity->getBirthday();
        $birthdayArrayData = $data['birthday'];
        $this->assertSame($birthdayArrayData['uid'], $birthday->getUid());
        $this->assertSame($birthdayArrayData['day'], $birthday->getDay());
        $this->assertSame($birthdayArrayData['month'], $birthday->getMonth());
        $this->assertSame($birthdayArrayData['year'], $birthday->getYear());

        // user.address data check
        /** @var \Aikrof\Hydrator\Tests\Fixtures\Entities\AddressEntity $address */
        $address = \current($entity->getAddress());
        $addressArrayData = \current($data['address']);
        $this->assertSame($addressArrayData['uid'], $address->getUid());
        $this->assertSame($addressArrayData['city'], $address->getCity());
        $this->assertSame($addressArrayData['street'], $address->getStreet());
        $this->assertSame($addressArrayData['houseNumber'], $address->getHouseNumber());
        $this->assertSame($addressArrayData['phone'], $address->getPhone());
    }
}