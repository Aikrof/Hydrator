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
 * Class ExtractTest
 *
 * @group Hydrator
 */
class ExtractTest extends TestCase
{
    /**
     * @var \Aikrof\Hydrator\Tests\Fixtures\Entities\UserEntity
     */
    protected $user;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        // Fill user entity
        $this->user = $this->fillUserEntity(new UserEntity());

        // unset fields that are ignored in UserEntity
        unset($this->userData['id'], $this->userData['paymentMethod']);
    }

    /**
     * Extract data from object to array.
     */
    public function testExtractFromObject(): void
    {
        $data = Hydrator::extract($this->user);

        // Check ignored fields
        $this->assertArrayNotHasKey('id', $data);
        $this->assertArrayNotHasKey('paymentMethod', $data);

        // User data check
        foreach ($this->userData as $field => $value) {
            @$this->assertSame($value, $data[$field]);
        }

        // User.birthday data check
        $birthday = $this->userBirthday;
        $hydrateUserBirthday = $data['birthday'];
        foreach ($birthday as $field => $value) {
            @$this->assertSame($value, $hydrateUserBirthday[$field]);
        }

        // User.address data check
        $address = \current($this->userData['address']);
        $hydrateUserAddress = \current($data['address']);
        foreach ($address as $field => $value) {
            @$this->assertSame($value, $hydrateUserAddress[$field]);
        }
    }

    /**
     * Extract user data and exclude some fields.
     */
    public function testExcludeFields(): void
    {
        $data = Hydrator::extract(
            $this->user,
            [
                'uid','password',
                'birthday.uid', 'birthday.day', 'birthday.month',
                'address.uid', 'address.phone'
            ]
        );

        // User exclude
        $this->assertArrayNotHasKey('uid', $data);
        $this->assertArrayNotHasKey('password', $data);

        // User.birthday exclude
        $this->assertArrayNotHasKey('uid', $data['birthday']);
        $this->assertArrayNotHasKey('day', $data['birthday']);
        $this->assertArrayNotHasKey('month', $data['birthday']);

        // User.address exclude
        $this->assertArrayNotHasKey('uid', \current($data['address']));
        $this->assertArrayNotHasKey('phone', \current($data['address']));
    }

    /**
     * Hide all null properties from array
     */
    public function testHideNullProperties(): void
    {
        // Set null for some fields in UserEntity
        $this->user
            ->setAddress([])
            ->setFirstName(null)
            ->setLastName(null);

        // Set null for field `uid` in BirthdayEntity
        $this->user->getBirthday()->setUid(null);

        $data = Hydrator::extract($this->user, [], true);

        // User data
        $this->assertArrayNotHasKey('address', $data);
        $this->assertArrayNotHasKey('firstName', $data);
        $this->assertArrayNotHasKey('lastName', $data);

        // User.birthday data
        $this->assertArrayNotHasKey('uid', $data['birthday']);
    }

    /**
     * Get default values if value is not set
     */
    public function testDefaultObjectValues(): void
    {
        $user = new UserEntity();

        $data = Hydrator::extract($user, [], true);
        foreach ($data as $field => $value) {
            $objectFieldValue = (function($field){
                return $this->{$field};
            })->call($user, $field);

            $this->assertSame($objectFieldValue, $value);
        }
    }

    /**
     * Empty object test
     */
    public function testException(): void
    {
        $this->expectException(HydratorExeption::class);
        $this->expectExceptionMessage('Cannot get properties from: `Aikrof\Hydrator\Tests\Fixtures\Entities\EmptyEntity`');
        Hydrator::extract(EmptyEntity::class);
    }
}