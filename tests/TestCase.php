<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Tests
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests;

use Aikrof\Hydrator\Tests\Fixtures\Entities\AddressEntity;
use Aikrof\Hydrator\Tests\Fixtures\Entities\BirthdayEntity;
use Aikrof\Hydrator\Tests\Fixtures\Entities\UserEntity;
use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 */
abstract class TestCase extends BaseTestCase
{
    /**
     * @var array
     */
    protected $userData;

    /**
     * @var array
     */
    protected $userAddress;

    /**
     * @var array
     */
    protected $userBirthday;

    public function setUp(): void
    {
        $this->userData = include realpath('./tests/Fixtures/Data/user.php');
        $this->userAddress = include realpath('./tests/Fixtures/Data/address.php');
        $this->userBirthday = include realpath('./tests/Fixtures/Data/birthday.php');
    }

    protected function fillUserEntity(object $userEntity): UserEntity
    {
        // Fill user address entity
        $addressEntity = new AddressEntity();
        $this->simpleFill($addressEntity, $this->userAddress);

        // Fill user birthday entity
        $birthdayEntity = new BirthdayEntity();
        $this->simpleFill($birthdayEntity, $this->userBirthday);

        // Fill user entity
        $userEntity = new UserEntity();
        $this->simpleFill($userEntity, $this->userData);
        // Set user.address
        $userEntity->setAddress([$addressEntity]);
        // Set user.birthday
        $userEntity->setBirthday($birthdayEntity);

        return $userEntity;
    }

    private function simpleFill(object $entity, array $data): void
    {
        (function ($data){
            foreach ($data as $field => $value) {
                $this->{$field} = $value;
            }
        })->call($entity, $data);
    }
}