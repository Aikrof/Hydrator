<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator\Core
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

/**
 * Class TypeEnum
 */
abstract class TypeEnum
{
    public const TYPE_STRING = 'string';
    public const TYPE_INT = 'int';
    public const TYPE_FLOAT = 'float';
    public const TYPE_BOOLEAN = 'bool';
    public const TYPE_OBJECT = 'object';
    public const TYPE_ARRAY = 'array';

    public const ARRAY_OF_OBJECT = 'object_array';
    public const ARRAY_OF_STRING = 'string_array';
    public const ARRAY_OF_INT = 'int_array';
    public const ARRAY_OF_FLOAT = 'float_array';

    public const SCALAR_TYPES = [
        self::TYPE_STRING,
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_BOOLEAN,
    ];

    public const ARRAY_TYPES = [
        self::ARRAY_OF_OBJECT,
        self::ARRAY_OF_STRING,
        self::ARRAY_OF_INT,
        self::ARRAY_OF_FLOAT,
    ];

    public const ALLOWED_TYPES = [
        self::TYPE_STRING,
        self::TYPE_INT,
        self::TYPE_FLOAT,
        self::TYPE_BOOLEAN,
        self::TYPE_ARRAY,
        self::TYPE_OBJECT,
        self::ARRAY_OF_OBJECT,
        self::ARRAY_OF_STRING,
        self::ARRAY_OF_INT,
        self::ARRAY_OF_FLOAT,
    ];
}