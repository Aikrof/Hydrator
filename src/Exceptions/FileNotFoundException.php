<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Exceptions
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Exceptions;

/**
 * Class FileNotFoundException
 *
 *  Exception class thrown when a file couldn't be found.
 */
class FileNotFoundException extends BaseException
{
    /**
     * FileNotFoundException constructor.
     *
     * @param string|null $message
     * @param string|null $path
     */
    public function __construct(string $message = null, string $path = null)
    {
        if (!$message) {
            if (!$path) {
                $message = 'File could not be found.';
            }
            else {
                $message = sprintf('File "%s" could not be found.', $path);
            }
        }

        parent::__construct($message);
    }
}