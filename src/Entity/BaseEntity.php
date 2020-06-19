<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Entity
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Entity;

use Aikrof\Hydrator\Traits\HydratorTrait;

/**
 * Class BaseEntity
 */
class BaseEntity implements EntityInterface
{
    use HydratorTrait;
}