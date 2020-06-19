<?php
/**
 * @link http://www.bintime.com
 * @copyright 2017 (c) Bintime
 * @package Gepard\Hydrator
 * @author Vadym Stepanov <vadym.stepanov@bintime.com>
 * @author Rusavskiy Vitaliy <rusavskiy.v@bintime.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Tests;

use Exception;
use function class_exists;
use function get_class;
use function in_array;
use function is_array;
use function is_bool;
use function is_float;
use function is_int;
use function is_numeric;
use function is_object;
use function is_scalar;
use function is_string;
use function mb_strtolower;

/**
 * Class FieldMapping
 *
 * Describes detailed field mapping information:
 * - field name
 * - data type (see constants)
 * - default value
 * - class name of the nested document
 *
 * This class also provide method to make php type cast for scalar data types.
 */
class FieldMapping
{
    public const TYPE_ARRAY = 'array';
    public const TYPE_ARRAY_FLOAT = 'array_float';
    public const TYPE_ARRAY_INT = 'array_int';
    public const TYPE_ARRAY_OBJECT = 'object_array';
    public const TYPE_ARRAY_STRING = 'array_string';
    public const TYPE_BOOLEAN = 'bool';
    public const TYPE_FLOAT = 'float';
    public const TYPE_INTEGER = 'int';
    public const TYPE_OBJECT = 'object';
    public const TYPE_STRING = 'string';

    /** @var array */
    public static $classesExists = [];
    /** @var string */
    public $className = '';
    /** @var mixed */
    public $defaultPropertyValue;
    /** @var string */
    public $field;
    /** @var bool */
    public $isKindOfObject;
    /** @var bool */
    public $objectAsNull;
    /** @var string */
    public $type;

    /**
     * FieldMapping constructor.
     */
    public function __construct()
    {
    }

    /**
     * Set field name
     *
     * @param string $value Field name.
     *
     * @return void
     * @throws HydratorException
     */
    public function setField(string $value): void
    {
        if (empty($value)) {
            throw new Exception('Field name must cannot be empty');
        }

        $this->field = $value;
    }

    /**
     * Set field data type
     *
     * @param string $value Field data type.
     *
     * @return void
     * @throws HydratorException
     */
    public function setType(string $value): void
    {
        $allowed = [
            self::TYPE_OBJECT,
            self::TYPE_ARRAY_OBJECT,
            self::TYPE_ARRAY,
            self::TYPE_ARRAY_INT,
            self::TYPE_ARRAY_FLOAT,
            self::TYPE_ARRAY_STRING,
            self::TYPE_INTEGER,
            self::TYPE_FLOAT,
            self::TYPE_STRING,
            self::TYPE_BOOLEAN,
        ];

        if (empty($value) || !in_array($value, $allowed, true)) {
            throw new Exception(
                'Type cannot be empty and should be one of the allowed types: "' . $value . '"'
            );
        }

        $this->type = $value;
        $this->isKindOfObject = null;
        $this->isKindOfObject();
    }

    /**
     * Set default value. If not null will check value according to data type (will throw exception if data type is
     * still not set)
     *
     * @param mixed $value Default field value.
     *
     * @return void
     * @throws HydratorException
     */
    public function setDefaultPropertyValue($value): void
    {
        if ($value !== null) {
            if (empty($this->type)) {
                throw new Exception('Cannot check default value according to field type - it is missing');
            }

            switch ($this->type) {
                case self::TYPE_ARRAY:
                case self::TYPE_ARRAY_INT:
                case self::TYPE_ARRAY_FLOAT:
                case self::TYPE_ARRAY_STRING:
                case self::TYPE_ARRAY_OBJECT:
                    if (!is_array($value)) {
                        throw new Exception('Wrong default value for array-type field');
                    }
                    break;

                case self::TYPE_OBJECT:
                    throw new Exception('Object-type field cannot have not null default value');

                default:
                    if (!is_scalar($value)) {
                        throw new Exception('Wrong default value for scalar-type field');
                    }
                    break;
            }
        } else {
            switch ($this->type) {
                case self::TYPE_ARRAY:
                case self::TYPE_ARRAY_INT:
                case self::TYPE_ARRAY_FLOAT:
                case self::TYPE_ARRAY_STRING:
                case self::TYPE_ARRAY_OBJECT:
                    $value = [];
                    break;
            }
        }

        $this->defaultPropertyValue = $value;
    }

    /**
     * Get nested document class name
     *
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className ?: '';
    }

    /**
     * Set name of the class intended to process data in this field
     *
     * @param string $value The class name.
     *
     * @return void
     * @throws HydratorException
     */
    public function setClassName(string $value): void
    {
        if ($value !== null) {
            if (!isset(self::$classesExists[$value]) && !(self::$classesExists[$value] = class_exists($value))) {
                throw new Exception('Specified class name is not valid - class not exists: "' . $value . '"');
            }
        }

        $this->className = $value;
    }

    /**
     * Get flag if field is any kind of object (single or multivalued)
     *
     * @return boolean
     */
    public function isKindOfObject(): bool
    {
        if ($this->isKindOfObject === null) {
            $this->isKindOfObject = in_array($this->type, [self::TYPE_OBJECT, self::TYPE_ARRAY_OBJECT], true);
        }

        return $this->isKindOfObject;
    }

    /**
     * Perform php type cast according to data type
     *
     * @param mixed $value Input value.
     *
     * @return mixed
     * @throws HydratorException
     */
    public function phpTypeCast($value)
    {
        switch ($this->type) {
            case self::TYPE_ARRAY:
            case self::TYPE_ARRAY_INT:
            case self::TYPE_ARRAY_FLOAT:
            case self::TYPE_ARRAY_STRING:
            case self::TYPE_ARRAY_OBJECT:
                if (empty($value)) {
                    $value = $this->defaultPropertyValue;
                    $value = $value ?: [];
                } elseif (!is_array($value)) {
                    throw new HydratorException('Wrong value for field "' . $this->field . '" of array type');
                }
                break;

            case self::TYPE_INTEGER:
                if ($value === '' || $value === null || $value === []) {
                    $value = $this->defaultPropertyValue;
                } elseif (is_int($value) || (is_numeric($value) && is_int((int)$value))) {
                    $value = (int)$value;
                } else {
                    throw new HydratorException('Wrong value for field "' . $this->field . '" of integer type');
                }
                break;

            case self::TYPE_FLOAT:
                if ($value === '' || $value === null || $value === []) {
                    $value = $this->defaultPropertyValue;
                } elseif (is_float($value) || (is_numeric($value) && is_float((double)$value))) {
                    $value = (double)$value;
                } else {
                    throw new HydratorException(
                        'Wrong value for field "' . $this->field . '" of float/double type'
                    );
                }
                break;

            case self::TYPE_STRING:
                try {
                    $value = ($value === '' || $value === null || $value === []) ? $this->defaultPropertyValue : (string)$value;
                } catch (Exception $e) {
                    throw  $e;
                }
                break;

            case self::TYPE_BOOLEAN:
                if ($value === null) {
                    $testValue = $this->defaultPropertyValue;
                } elseif (is_string($value)) {
                    $testValue = mb_strtolower($value);
                } else {
                    $testValue = $value;
                }

                // Boolean data type: https://www.elastic.co/guide/en/elasticsearch/reference/2.3/boolean.html
                $value = !in_array($testValue, [false, 'false', 'off', 'no', '0', '', 0, 0.0, null], true);
                // Treating a 0 bit value as false too
                // See: https://github.com/yiisoft/yii2/issues/9006
                $value = (bool)$value && $value !== "\0";
                break;

            default:
                //TODO: FIX! Check object type!
//                // Do not typecast ObjectID instances
//                if (\is_object($value)
//                    && isset(self::$classesExists['\MongoDB\BSON\ObjectID'])
//                    && $value instanceof ObjectID
//                ) {
//                    return $value;
//                }
                break;
        }

        return $value;
    }

    /**
     * Check value type according to field mapping type
     *
     * @param mixed $value Value to check.
     *
     * @return mixed
     * @throws HydratorException
     */
    public function checkValue($value)
    {
        switch ($this->type) {
            case self::TYPE_BOOLEAN:
            case self::TYPE_FLOAT:
            case self::TYPE_INTEGER:
            case self::TYPE_STRING:
                if (!is_string($value) && !is_bool($value) && !is_numeric($value) && null !== $value
                    && false === (
                        is_object($value)
                        && isset(self::$classesExists['\MongoDB\BSON\ObjectID'])
                        && $value instanceof ObjectID
                    )
                ) {
                    throw new HydratorException('Wrong value for field "' . $this->field . '" of scalar type');
                }
                break;

            case self::TYPE_ARRAY:
            case self::TYPE_ARRAY_INT:
            case self::TYPE_ARRAY_FLOAT:
            case self::TYPE_ARRAY_STRING:
                if (!is_array($value)) {
                    throw new HydratorException(
                        'Wrong value for field "' . $this->field . '" of scalar array type'
                    );
                }
                break;

            case self::TYPE_ARRAY_OBJECT:
                if (!is_array($value)) {
                    throw new HydratorException(
                        'Field "' . $this->field . '" was treated as objects array but value is not an array'
                    );
                }
                $className = $this->className;
                foreach ($value as $item) {
                    if (!($item instanceof $className)) {
                        throw new HydratorException(
                            'One of the field values is not an object or object of invalid type'
                        );
                    }
                }
                break;

            case self::TYPE_OBJECT:
                if (!($value === '' || $value === null || $value === [])) {
                    $className = $this->className;
                    if (!($value instanceof $className)) {
                        throw new HydratorException(
                            'Field "' . $this->field . '" value is not an object or object of invalid type: '
                            . $className . ' instanceof '
                            . (is_object($value) ? get_class($value) : print_r($value, true))
                        );
                    }
                }
                break;
        }

        return $value;
    }
}
