<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Tests\Fixtures\Entities
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests\Fixtures\Entities;

/**
 * Class BirthdayEntity
 */
class BirthdayEntity
{
    /**
     * @var string|null
     */
    protected $uid;

    /**
     * @var int
     */
    protected $day;

    /**
     * @var int
     */
    protected $month;

    /**
     * @var int
     */
    protected $year;

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
     * @return int
     */
    public function getDay(): int
    {
        return $this->day;
    }

    /**
     * @param int $day
     *
     * @return static
     */
    public function setDay(int $day): self
    {
        if ($day > 0 && $day < 32) {
            $this->day = $day;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getMonth(): int
    {
        return $this->month;
    }

    /**
     * @param int $month
     *
     * @return static
     */
    public function setMonth(int $month): self
    {
        if ($month > 0 && $month < 13) {
            $this->month = $month;
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): int
    {
        return $this->year;
    }

    /**
     * @param int $year
     *
     * @return static
     */
    public function setYear(int $year): self
    {
        if ($year > 1940 && $year < date('Y')) {
            $this->year = $year;
        }

        return $this;
    }
}