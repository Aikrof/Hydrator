<?php
/**
 * @link https://github.com/Aikrof
 * @package Aikrof\Hydrator
 * @author Denys <AikrofStark@gmail.com>
 */

declare(strict_types = 1);

namespace Aikrof\Hydrator\Core;

use Aikrof\Hydrator\Components\Instance;
use Aikrof\Hydrator\Interfaces\ReflectionInterface;
use Aikrof\Hydrator\Interfaces\ServiceHydratorInterface;
use Aikrof\Hydrator\Exceptions\HydratorExeption;

/**
 * Class ServiceHydrator
 */
final class ServiceHydrator implements ServiceHydratorInterface
{
    /**
     * @var \Aikrof\Hydrator\Interfaces\ReflectionInterface
     */
    private $reflection;

    /**
     * ServiceHydrator constructor.
     *
     * @param \Aikrof\Hydrator\Interfaces\ReflectionInterface|null $reflection
     */
    public function __construct(ReflectionInterface $reflection = null)
    {
        $this->reflection = $reflection ?: Instance::create(ReflectionInterface::class);
    }

    /**
     * @param object $entity Object entity.
     * @param bool   $hideNullProperties If set `$hideNullProperties` = false null properties will be mapped to array only if:
     *      1. If in field annotation we set null like compound property (string|null).
     *      2. If we haven't any annotation for field.
     *      3. If in field annotation we have `@internal` tag.
     * @param array  $exclude define list of fields that should be excluded.
     * @param bool   $recursiveCall
     *
     * @return array
     *
     */
    public function extractFromEntity(
        object $entity,
        bool $hideNullProperties,
        array $exclude,
        bool $recursiveCall = false
    ): array {
        $class = \get_class($entity);

        $this->validateEntity($entity, $class);

        $exclude = $recursiveCall === false ? array_flip($exclude) : $exclude;

        /** @var \Aikrof\Hydrator\Core\Mapper[] */
        $mappings = $this->reflection->getMappings($class);

        if (!$objectFields = \array_keys($mappings)) {
            return [];
        }

        $data = $this->extractObjectValues($entity, $objectFields);

        $result = [];
        foreach ($data as $field => $property) {
            // Exclude fields
            if (!$recursiveCall && \array_key_exists($field, $exclude)) {
                unset($exclude[$field]);
                continue;
            }

            $mapping = $mappings[$field];

            $value = null;
            if (!empty($property)) {
                if ($mapping->isObject()) {
                    if ($mapping->getType() === TypeEnum::ARRAY_OF_OBJECT) {
                        $nestedProperties = [];
                        foreach ($property as $key => $nestedObject) {
                            $nestedProperties[$key] = $this->extractFromEntity($nestedObject, $hideNullProperties, $exclude, true);
                        }

                        $value = $nestedProperties;
                    }
                    else {
                        $value = $this->extractFromEntity($property, $hideNullProperties, $exclude, true);
                    }
                }
                else {
                    $value = $property;
                }
            }

            if (empty($value)) {
                if ($hideNullProperties === true && empty($mapping->getDefaultValue()) && !$mapping->isNullDefault()) {
                    continue;
                }

                $value = $mapping->getDefaultValue();
            }

            // Exclude nested fields
            if (!$recursiveCall && !empty($exclude) && \is_array($value)) {
                $fieldLength = \strlen($field);
                foreach ($exclude as $excludeKey => $ExcludeVal) {
                    if (strncmp($excludeKey, $field, $fieldLength) === 0) {
                        $nested = \explode('.', $excludeKey);
                        unset($nested[0]);

                        if ($this->excludeNestedFields($nested, $value) === true) {
                            unset($exclude[$excludeKey]);
                        }
                    }
                }
            }

            $result[$field] = $value;
        }

        return $result;
    }

    /**
     * Create entity and hydrate data from array to this entity.
     *
     * @param array     $data
     * @param string    $class
     *
     * @return object
     */
    public function createEntityAndHydrate(array $data, string $class): object
    {
        $entity = Instance::create($class);

        return $this->hydrateToEntity($data, $entity);
    }

    /**
     * Hydrate data from array to entity.
     *
     * @param array     $data
     * @param object    $entity
     *
     * @return object
     */
    public function hydrateToEntity(array $data, object $entity): object
    {
        $class = \get_class($entity);

        /** @var \Aikrof\Hydrator\Core\Mapper[] */
        $mappings = $this->reflection->getMappings($class);

        $entityData = [];
        foreach ($mappings as $field => $mapping) {
            $fieldValue = $data[$field] ?? $mapping->getDefaultValue();

            if ($mapping->isObject()) {
                if ($mapping->getType() === TypeEnum::ARRAY_OF_OBJECT) {
                    $nestedProperties = [];
                    foreach ($fieldValue as $key => $nestedValue) {
                        $nestedProperties[$key] = $this->createEntityAndHydrate(
                            $nestedValue ?: [], $mapping->getClassName()
                        );
                    }

                    $value = $nestedProperties;
                }
                else {
                    $value = $this->createEntityAndHydrate($fieldValue ?: [], $mapping->getClassName());
                }
            }
            else {
                $value = $fieldValue;
            }

            // check value type and object field type
            $entityData[$field] = $mapping->checkValueType($value) === true ? $value :  $mapping->getDefaultValue();
        }

        $this->fillEntityFields($entity, $entityData);

        return $entity;
    }

    /**
     * @param object    $entity
     * @param string    $class
     *
     * @return void
     *
     * @throws \Aikrof\Hydrator\Exceptions\HydratorExeption
     */
    private function validateEntity(object $entity, string $class): void
    {
        /** @var bool $isObject */
        $isObject = \is_object($entity);

        if (!$isObject) {
            throw new HydratorExeption("Entity mast be an object {$entity}");
        }

        if ($isObject && empty($entity)) {
            throw new HydratorExeption("Cannot get properties from: {$class}.");
        }
    }

    /**
     * Extract all values from the given object.
     *
     * @param object $entity
     * @param array  $objectFields
     *
     * @return array|null
     */
    private function extractObjectValues(object $entity, array $objectFields): ?array
    {
        return (function ($objectFields) {
            $data = [];
            foreach ($objectFields as $field) {
                $data[$field] = $this->{$field};
            }

            return $data;
        })->call($entity, $objectFields);
    }

    /**
     * Exclude nested fields from array.
     *
     * @param array $exclude
     * @param array $data
     *
     * @return bool
     */
    private function excludeNestedFields(array $exclude, array &$data): bool
    {
        $current = \current($exclude);
        $next = \next($exclude);

        if (!isset($data[$current])) {
            return false;
        }
        
        if ($next === false) {
            unset($data[$current]);
            return true;
        }

        return $this->excludeNestedFields($exclude, $data[$current]);
    }

    /**
     * @param object $entity
     * @param array  $data
     *
     * @return void
     */
    private function fillEntityFields(object $entity, array $data): void
    {
        (function ($data) {
            foreach ($data as $field => $value) {
                $this->{$field} = $value;
            }
        })->call($entity, $data);
    }
}