<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Exceptions
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Exceptions;

/**
 * Class ClassNotFoundException
 */
class ClassNotFoundException extends BaseException
{
    /**
     * @var string
     */
    private $className;

    /**
     * ClassNotFoundException constructor.
     *
     * @param string $className
     */
    public function __construct(string $className)
    {
        $thisIsInterface = \interface_exists($className);

        if ($thisIsInterface) {
            $message = 'Interface ' . $className . ' is not attached for any class.';
        }
        else {
            $message = 'Class ' . $className . ' is not exist.';
        }

        parent::__construct($message, $className);
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }
}