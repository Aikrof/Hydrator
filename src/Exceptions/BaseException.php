<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Exceptions
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Exceptions;

use RuntimeException;

/**
 * Class BaseException
 */
abstract class BaseException extends RuntimeException
{
    /**
     * @var string|null
     */
    private $className;

    /**
     * BaseException constructor.
     *
     * @param string $message
     * @param string $className
     */
    public function __construct(string $message, string $className = null)
    {
        parent::__construct($message);

        $this->className = $className;
    }

    /**
     * @return string|null
     */
    public function getClassName(): ?string
    {
        return $this->className;
    }
}