<?php

declare(strict_types=1);

namespace Aikrof\Hydrator\Tests;

use Aikrof\Hydrator\Hydrator;

class TestEntity
{
    /**
     * @var \Aikrof\Hydrator\Tests\Infoentity[]
    */
    protected $data;

    /**
     * asdasd
     * asd
     * asd
     *
     * @var array
     **/
    protected $firstName = ['1'];

    /**
     * @var string
     */
    protected $lastName;


    public function exclude()
    {
        return ['lastName'];
    }
//
//    /**
//     * @var int
//     */
//    protected $age;
//
//    /**
//     * @var string
//     */
//    protected $birthday;

//    /**
//     * @var Infoentity
//     */
//    protected $info;

    /**
     * @return \Aikrof\Hydrator\Tests\Infoentity
     */
    public function getData(): \Aikrof\Hydrator\Tests\Infoentity
    {
        return $this->data;
    }

    /**
     * @param array $data
     */
    public function setInfo($data): void
    {
        $this->data = $data;
    }


    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $firstName
     */
    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    /**
     * @return string
     */
    public function getLastName(): string
    {
        return $this->lastName;
    }

    /**
     * @param string $lastName
     */
    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @return string
     */
    public function getBirthday(): string
    {
        return $this->birthday;
    }

    /**
     * @param string $birthday
     */
    public function setBirthday(string $birthday): void
    {
        $this->birthday = $birthday;
    }
}