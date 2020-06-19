<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Exceptions
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Exceptions;

/**
 * Class HydratorExeption
 */
class HydratorExeption extends BaseException
{
    public function __construct(string $message, string $className = null)
    {
        parent::__construct($message, $className);
    }
}