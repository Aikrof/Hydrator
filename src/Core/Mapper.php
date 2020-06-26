<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Exceptions\HydratorExeption;

/**
 * Class Mapper
 */
class Mapper
{
    /**
     * Entity class that will be mapped
     *
     * @var string
     */
    protected $entityClass;

    /** @var string */
    protected $type;

    /** @var bool */
    protected $isArrayType = false;

    /** @var string */
    protected $className = '';

    /** @var string */
    protected $field;

    /** @var mixed */
    protected $defaultValue;

    /** @var bool */
    protected $isObject = false;

    /** @var bool */
    protected $isNullDefault = false;

    /**
     * Mapper constructor.
     *
     * @param string $entityClass
     */
    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }

    /**
     * When clone, set fields to default values.
     */
    public function __clone()
    {
        $this->type = null;
        $this->className = '';
        $this->field = null;
        $this->defaultValue = null;
        $this->isObject = false;
        $this->isNullDefault = false;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     *
     * @return static
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function setType(string $type): self
    {
        if (!\in_array($type, TypeEnum::ALLOWED_TYPES, true)) {
            throw new HydratorExeption(
                "Data type `{$type}` in class: `{$this->entityClass}` is not allowed."
            );
        }

        if (\in_array($type, TypeEnum::ARRAY_TYPES, true)) {
            $this->isArrayType = true;
        }

        $this->type = $type;

        return $this;
    }

    /**
     * @return bool
     */
    public function isArrayType(): bool
    {
        return $this->isArrayType;
    }

    /**
     * @return string
     */
    public function getClassName(): string
    {
        return $this->className;
    }

    /**
     * @param string $className
     *
     * @return static
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    public function setClassName(string $className): self
    {
        if (!\class_exists($className)) {
            throw new HydratorExeption(
                "Class annotation in: `{$this->entityClass}` is not valid - class: `{$className}` does not exist."
            );
        }

        $this->className = $className;

        return $this;
    }

    /**
     * @return string
     */
    public function getField(): ?string
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return static
     */
    public function setField(string $field): self
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @param $value
     *
     * @return static
     */
    public function setDefaultValue($value): self
    {
        if ($value !== null) {
            if (empty($this->type)) {
                throw new HydratorExeption('Cannot check value according to field type - it is missing.');
            }

            if (!\is_scalar($value) && \in_array($this->type, TypeEnum::SCALAR_TYPES, true)) {
                throw new HydratorExeption('Wrong default value for scalar-type field.');
            }
            else if (!\is_array($value) && \in_array($this->type, TypeEnum::ARRAY_TYPES, true)) {
                throw new HydratorExeption('Wrong default value for array-type field.');
            }
            else if (!\is_object($value) && $this->type === TypeEnum::TYPE_OBJECT) {
                throw new HydratorExeption('Object-type field cannot have not null default value.');
            }
        }
        else {
            if (\in_array($this->type, TypeEnum::ARRAY_TYPES, true)) {
                $value = [];
            }
        }

        $this->defaultValue = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isObject(): bool
    {
        return $this->isObject;
    }

    /**
     * @param bool $isObject
     *
     * @return static
     */
    public function setIsObject(bool $isObject): self
    {
        $this->isObject = $isObject;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNullDefault(): bool
    {
        return $this->isNullDefault;
    }

    /**
     * @param bool $isNullDefault
     *
     * @return static
     */
    public function setIsNullDefaul(bool $isNullDefault): self
    {
        $this->isNullDefault = $isNullDefault;

        return $this;
    }

    /**
     * Check given value type according to mapped object type.
     *
     * @param mixed     $value
     *
     * @return bool
     */
    public function checkValueType($value): bool
    {
        if ($this->type === null) {
            return true;
        }

        $valueType = TypeEnum::getValueType($value);

        if ($valueType === TypeEnum::TYPE_ARRAY &&
            \in_array($this->type, TypeEnum::ARRAY_TYPES, true)
        ) {
            $nestedType = null;
            switch ($this->type) {
                case TypeEnum::TYPE_ARRAY:
                    $nestedType = TypeEnum::TYPE_ARRAY;
                    break;
                case TypeEnum::ARRAY_OF_OBJECT:
                    $nestedType = TypeEnum::TYPE_OBJECT;
                    break;
                case TypeEnum::ARRAY_OF_STRING:
                    $nestedType = TypeEnum::TYPE_STRING;
                    break;
                case TypeEnum::ARRAY_OF_INT:
                    $nestedType = TypeEnum::TYPE_INT;
                    break;
                case TypeEnum::ARRAY_OF_FLOAT:
                    $nestedType = TypeEnum::TYPE_FLOAT;
                    break;
            }

            foreach ($value as $valueField) {
                if (TypeEnum::getValueType($valueField) !== $nestedType) {
                    return false;
                }
            }

            return true;
        }

        return ($this->type === $valueType);
    }
}