<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Tests\Fixtures\Entities
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests\Fixtures\Entities;

use Aikrof\Hydrator\Traits\HydratorTrait;

/**
 * Class UserEntity
 */
class UserEntity
{
    use HydratorTrait;

    /**
     * @var string|null
     */
    protected $uid = '0000-0000-0000-0000';

    /**
     * @var string|null
     */
    protected $login = 'default';

    /**
     * @var string|null
     */
    protected $password;

    /**
     * @var string|null
     */
    protected $firstName;

    /**
     * @var string|null
     */
    protected $lastName;

    /**
     * @var string|null
     */
    protected $email = 'default@email.com';

    /**
     * @var BirthdayEntity|null
     */
    protected $birthday;

    /**
     * @var AddressEntity[]|null
     */
    protected $address;

    /**
     * @var string
     *
     * @ignore
     */
    protected $id;

    /**
     * @var string
     *
     * @ignore
     */
    protected $paymentMethod;

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string|null $uid
     *
     * @return static
     */
    public function setUid(?string $uid): self
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    /**
     * @param string|null $login
     *
     * @return static
     */
    public function setLogin(?string $login): self
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string|null $password
     *
     * @return static
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    /**
     * @param string|null $firstName
     *
     * @return static
     */
    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    /**
     * @param string|null $lastName
     *
     * @return static;
     */
    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     *
     * @return static
     */
    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return BirthdayEntity
     */
    public function getBirthday(): BirthdayEntity
    {
        return $this->birthday;
    }

    /**
     * @param BirthdayEntity $birthday
     *
     * @return static
     */
    public function setBirthday(?BirthdayEntity $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    /**
     * @return array
     */
    public function getAddress(): array
    {
        return $this->address;
    }

    /**
     * @param \Aikrof\Hydrator\Tests\Fixtures\Entities\AddressEntity[] $address
     *
     * @return static
     */
    public function setAddress(array $address): self
    {
        $this->address = $address;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return static
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentCard
     *
     * @return static
     */
    public function setPaymentCard(string $paymentCard): self
    {
        $this->paymentCard = $paymentCard;

        return $this;
    }
}