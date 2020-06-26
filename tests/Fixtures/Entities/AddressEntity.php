<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Tests\Fixtures\Entities
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests\Fixtures\Entities;

/**
 * Class Address
 */
class AddressEntity
{
    /**
     * @var string
     */
    protected $uid;

    /**
     * @var string
     */
    protected $city;

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string
     */
    protected $houseNumber;

    /**
     * @var string
     */
    protected $phone;

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     *
     * @return static
     */
    public function setUid(string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     *
     * @return static
     */
    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     *
     * @return static
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return string
     */
    public function getHouseNumber(): string
    {
        return $this->houseNumber;
    }

    /**
     * @param string $houseNumber
     *
     * @return static
     */
    public function setHouseNumber(string $houseNumber): self
    {
        $this->houseNumber = $houseNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     *
     * @return static
     */
    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }
}